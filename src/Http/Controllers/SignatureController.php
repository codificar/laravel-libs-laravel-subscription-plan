<?php

namespace Codificar\LaravelSubscriptionPlan\Http\Controllers;

use Codificar\LaravelSubscriptionPlan\Models\Transaction;
use Codificar\LaravelSubscriptionPlan\Models\Settings;

use Codificar\LaravelSubscriptionPlan\Models\Signature;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Codificar\LaravelSubscriptionPlan\Http\Requests\CancelSubscriptionRequest;
use Codificar\LaravelSubscriptionPlan\Http\Requests\SubscriptionDetailsFormRequest;
use Codificar\LaravelSubscriptionPlan\Http\Requests\UpdateSignatureProvider;

use Codificar\LaravelSubscriptionPlan\Http\Resources\SubscriptionDetailResource;
use Codificar\LaravelSubscriptionPlan\Http\Resources\UpdatePlanResource;
use Codificar\LaravelSubscriptionPlan\Jobs\NewSubscriptionMail;
use Artisan;
use Carbon\Carbon;
use Finance;
use Codificar\PaymentGateways\Libs\PaymentFactory;

class SignatureController extends Controller
{
    /**
     * Generate a signature if gateway->charge() returns success updating
     * the signature_id column in the provider table with the id of the generated signature
     *
     * @param UpdateSignatureProvider $request
     * @return UpdatePlanResource
     */
    public function newProviderSubscription(UpdateSignatureProvider $request) 
    {
        $plan       = $request->plan;
        $provider   = $request->provider;
        $payment    = $request->payment;
        $typeCharge = $request->charge_type;

        $hasSignature = Signature::getActiveProviderSignature($provider->id);
        $recurrence = $hasSignature ? true : false;

        $requestSubscriptionCharge = $this->RequestSubscriptionCharge($plan, $provider, $payment, $typeCharge, $recurrence);
        return new UpdatePlanResource($requestSubscriptionCharge);
    }

    /**
     * Makes the payment request for the plan
     * 
     * @param $plan Model plan
     * @param $provider Model provider
     * @param $payment Model payment
     * @param string $typeCharge
     * @return array
     */
    public function RequestSubscriptionCharge($plan, $provider, $payment, $typeCharge, $recurrence = false)
    {
       
        // Set the subscription expiration date
        $period = $plan->period;
        if ($recurrence) {
            $period = $plan->period + Settings::getDaysForSubscriptionRecurrency();
        }
        $nextExpiration = Carbon::now()->addDays($period);
        $value          = $plan->plan_price;

        if ($typeCharge == 'billet') {
            $transaction = $this->doBilletCharge($provider, $value, $typeCharge);
        } else if($typeCharge == 'gatewayPix') {
            $transaction = $this->doPixCharge($provider, $value, $typeCharge);
        } 
        else {
            $transaction = $this->doCreditCardCharge($provider, $value,$payment, $typeCharge);
        }

        if (isset($transaction['success']) && !$transaction['success']) {
            return $transaction;
        }

        $data = [
            'success' => true,
            'message' => trans('user_provider_web.signature_success')
        ];

        $activity = 1;
        $signature = Signature::updateProviderSignature($provider->signature_id, $provider->id, $plan->id, $nextExpiration, $transaction->id, $activity, $typeCharge, $payment);
        $provider->updateSignatureId($signature->id);
        $transaction->setSignatureId($signature->id);

        $data['signature_id'] = $signature->id;
        $data['transaction_db_id'] = $transaction->id;
        $data['charge_type'] = $typeCharge;

        if($typeCharge == 'billet') {
            NewSubscriptionMail::dispatch($signature);
        }

        return $data;
    }

    /**
     * Get a list of signatures
     * 
     * @return view
     */
    public function list()
    {
        $signatures = Signature::getList();
        $jsonSignatures = json_encode($signatures);
        $view = view('subscriptionPlan::signature.list', ['signatures'=>$jsonSignatures]); 
        return $view;
    }

    /**
     * Returns details of the current subscription
     * 
     * @param Request $request
     * @return query
     */
    public function query(Request $request){
        $model = new Signature;
        $query = $model->querySearch($request);
		return $query;
    }

     /**
     * Returns details of the current subscription
     * 
     * @param $id
     * @return void
     */
    public function suspendOrActivate($id){
        $signature = Signature::find($id);
        if ($signature->activity == 1) {
            $signature->activity = 0;
        } elseif ($signature->activity == 0) {
            $signature->activity = 1;
        }

        $signature->save();
    }

    /**
     * Returns details of the current subscription
     * 
     * @param SubscriptionDetailsFormRequest $request
     * @return SubscriptionDetailResource
     */
    public function getDetails (SubscriptionDetailsFormRequest $request)
    {
        return new SubscriptionDetailResource([
            'signature' => $request->signature, 
            'transaction' => $request->transaction
        ]);
    }

    /**
     * Cancel a subscription
     * 
     * @param CancelSubscriptionRequest $request
     * @return json
     */
    public function cancelSubscription(CancelSubscriptionRequest $request)
    {
        return response()->json([
            'success' => $request->subscription->cancel()
        ]);
    }

    /**
     * Tests the change of status of the billet in the case of pagame
     * 
     * @param int $id
     * @return json
     */
    public function testBilletPayment($id)
    {
        try {
            $signature = Signature::find($id);
            $gateway = PaymentFactory::createGateway();
            
            if ($signature && get_class($gateway) == 'PagarmeLib' && env('APP_ENV') != 'production') {
                $gateway->testBilletPaid($signature->transaction->gateway_transaction_id);
                Artisan::call('command:billetstatus');

                return response()->json($gateway->retrieve($signature->transaction));
            };

            return response()->json(['erro' => true]);
        } catch (\Throwable $th) {
            return response()->json(['erro' => $th->getMessage()]);
        }
    }

    /**
     * handle message response
     * @param array $response
     * @return string
     */
    private function getMessageError(array $response)
    {   
        if(isset($response['error']['messages'])) {
            return $response['error']['messages'];
        } 
        if( isset($response['messages']) ){
            return $response['messages'];
        }

        if(isset($response['original_message'])) {
            return $response['message'];
        }

        return trans('user_provider_web.payment_fail');
    }

    /**
     * Do billet Charge
     * @param Provider $provider
     * @param float $value
     * @param string $chargeType
     * 
     * @return array|Transaction
     */
    private function doBilletCharge($provider, float $value, $chargeType)
    {
        $gateway = PaymentFactory::createGateway();
        $response = $gateway->billetCharge(
            $value, 
            $provider, 
            route('GatewayPostbackBillet'), 
            Carbon::now()->addDays(Settings::getBilletExpirationDays())->toIso8601String(),
            Settings::getBilletInstructions()
        );

        if ($response && !$response['success']) {
            $message = $this->getMessageError($response);
            return [
                'success' => false,
                'pix' =>null,
                'charge_type'=>$chargeType,
                'message' => $message,
                'error' => trans('user_provider_web.payment_fail')
            ];
        }

        return $this->saveTransactionCharge($response, $provider, $value);

    }

    /**
     * Do pix Charge
     * @param Provider $provider
     * @param float $value
     * @param string $chargeType
     * 
     * @return array|Transaction
     */
    private function doPixCharge($provider, float $value, $chargeType)
    {

        $gateway = PaymentFactory::createPixGateway();
        $response = $gateway->pixCharge($value, $provider);

        if ($response && !$response['success']) {
            $message = $this->getMessageError($response);
            return [
                'success' => false,
                'pix' =>null,
                'charge_type'=>$chargeType,
                'message' => $message,
                'error' => trans('user_provider_web.payment_fail')
            ];
        }

        return $this->saveTransactionCharge($response, $provider, $value, true);

    }

    /**
     * Do Credit Card Charge
     * @param Provider $provider
     * @param float $value
     * @param Payment $payment
     * @param string $chargeType
     * 
     * @return array|Transaction
     */
    private function doCreditCardCharge($provider, float $value, $payment, $chargeType)
    {
        $gateway = PaymentFactory::createGateway();
        $description    = Finance::SIGNATURE_DEBIT;
        $response = $gateway->charge($payment, $value, $description, true);

        
        if ($response && !$response['success']) {
            $message = $this->getMessageError($response);
            return [
                'success' => false,
                'pix' =>null,
                'charge_type'=>$chargeType,
                'message' => $message,
                'error' => trans('user_provider_web.payment_fail')
            ];
        }

        return $this->saveTransactionCharge($response, $provider, $value);

    }

    /**
     * Save TRansaction charge
     * @param array $response
     * @param Provider $provider
     * @param float $value
     * @param bool $isPix
     * 
     * @return Transaction
     */
    private function saveTransactionCharge($response, $provider, $value, $isPix = false)
    {
        $gateway = PaymentFactory::createGateway();

        $billetUrl = array_key_exists('billet_url', $response) ? $response['billet_url'] : null;

        $pixBase64 = null;
        $pixCopyPaste = null;
        $pixExpirationDateTime = null;
        
        if($isPix) {
            $gateway = PaymentFactory::createPixGateway();
            $pixBase64 = $response['qr_code_base64'];
            $pixCopyPaste = $response['copy_and_paste'];
            $pixExpirationDateTime = $response['expiration_date_time'];
        }

        $paymentTax     = $gateway->getGatewayTax();
        $paymentFee     = $gateway->getGatewayFee();

        return Transaction::saveSignatureTransaction(
            $response['status'], 
            $value, 
            $paymentTax, 
            $paymentFee, 
            $response["transaction_id"],
            $billetUrl,
            $provider->ledger->id,
            $pixBase64,
            $pixCopyPaste,
            $pixExpirationDateTime
        );

    }
}