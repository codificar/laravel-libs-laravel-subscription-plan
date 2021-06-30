<?php

namespace Codificar\LaravelSubscriptionPlan\Http\Controllers;

use Codificar\LaravelSubscriptionPlan\Models\Transaction;
use Codificar\LaravelSubscriptionPlan\Models\Settings;
use Codificar\LaravelSubscriptionPlan\Models\Plan;

use Codificar\LaravelSubscriptionPlan\Models\Signature;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Codificar\LaravelSubscriptionPlan\Http\Requests\CancelSubscriptionRequest;
use Codificar\LaravelSubscriptionPlan\Http\Requests\PlataformSubscriptionDetailsRequest;
use Codificar\LaravelSubscriptionPlan\Http\Requests\SubscriptionDetailsFormRequest;
use Codificar\LaravelSubscriptionPlan\Http\Requests\UpdateSignatureProvider;

use Codificar\LaravelSubscriptionPlan\Http\Resources\PlataformSubscriptionDetailsResource;
use Codificar\LaravelSubscriptionPlan\Http\Resources\SubscriptionDetailResource;
use Codificar\LaravelSubscriptionPlan\Http\Resources\UpdatePlanResource;
use Codificar\LaravelSubscriptionPlan\Jobs\NewSubscriptionMail;
use Artisan;
use Carbon\Carbon;
use Finance;
use PaymentFactory;

class SignatureController extends Controller
{
    /**
     * Gera uma assinatura caso gateway->charge() retorne success atualizando
     * a coluna signature_id na tabela provider com o id da assinatura gerada
     *    
     * @return json object com a resposta success true ou false 
     */
    public function newProviderSubscription(UpdateSignatureProvider $request) 
    {
        $plan       = $request->plan;
        $provider   = $request->provider;
        $payment    = $request->payment;
        $typeCharge = $request->charge_type;

        $requestSubscriptionCharge = $this->RequestSubscriptionCharge($plan, $provider, $payment, $typeCharge);

        return new UpdatePlanResource($requestSubscriptionCharge);
    }

    /**
     * Realiza a requisição de pagamento para o plano
     * @param $plan Model plan
     * @param $provider Model provider
     * @param $payment Model payment
     * @param string $typeCharge
     * @return array
     */
    public function RequestSubscriptionCharge ($plan, $provider, $payment, $typeCharge, $recurrence = false)
    {
        $gateway        = PaymentFactory::createGateway();
        $paymentTax     = $gateway->getGatewayTax();
        $paymentFee     = $gateway->getGatewayFee();

        // Define a data de expiração da assinatura
        $period = $plan->period;
        if ($recurrence) {
            $period = $plan->period + Settings::getDaysForSubscriptionRecurrency();
        }
        $nextExpiration = Carbon::now()->addDays($period);
        $description    = Finance::SIGNATURE_DEBIT;
        $value          = $plan->plan_price;

        if ($typeCharge == 'billet') {
            $return = $gateway->billetCharge(
                $value, 
                $provider, 
                route('SubscriptionPostback'), 
                Carbon::now()->addDays(Settings::getBilletExpirationDays())->toIso8601String(),
                Settings::getBilletInstructions()
            );
        } else {
            $return = $gateway->charge($payment, $value, $description, true);
        }

        if (!$return['success']) {
            return [
                'success' => false,
                'message' => trans('user_provider_web.payment_fail'),
                'error' => trans('user_provider_web.payment_fail')
            ];
        }

        $data = [
            'success' => true,
            'message' => trans('user_provider_web.signature_success')
        ];

        $billetUrl = array_key_exists('billet_url', $return) ? $return['billet_url'] : null;

        $transaction = Transaction::saveSignatureTransaction(
            $return['status'], 
            $value, 
            $paymentTax, 
            $paymentFee, 
            $return["transaction_id"],
            $billetUrl
        );

        $activity = 1;
        $signature = Signature::updateProviderSignature($provider->signature_id, $provider->id, $plan->id, $nextExpiration, $transaction->id, $activity, $typeCharge, $payment);
        $provider->updateSignatureId($signature->id);
        $transaction->setSignatureId($signature->id);
        $data['signature_id'] = $signature->id;
        
        if($billetUrl) {
            $data['billet_url'] = $billetUrl;
            NewSubscriptionMail::dispatch($signature);
        }

        return $data;
    }

    public function list()
    {
        $signatures = Signature::getList();
        $jsonSignatures = json_encode($signatures);
        $view = view('subscriptionPlan::signature.list', ['signatures'=>$jsonSignatures]); 
        return $view;
    }

    public function query(Request $request){
        $model = new Signature;
        $query = $model->querySearch($request);
		return $query;
    }

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
     * Retorna detalhes da assinatura atual
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
     * Details if the plataform uses or not the subscription module
     * @param PlataformSubscriptionDetails $request
     * @return json
     */
    public function plataformRequireSubscription(PlataformSubscriptionDetailsRequest $request){

        $requiredPlan = null;
        $plans = null;
        
        if($request->location)
            $plans = Plan::getPlansListForProvider($request->location->id);

        if($plans){
            foreach ($plans as $plan) {
                if($plan->required){
                    $requiredPlan = $plan;
                    break;
                }
            }
        }

        return new PlataformSubscriptionDetailsResource(['plan' => $requiredPlan]);
    }

    /**
     * Testa a mudança de status do boleto no caso da pagarme
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
}