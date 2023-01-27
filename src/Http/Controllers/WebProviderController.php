<?php
namespace Codificar\LaravelSubscriptionPlan\Http\Controllers;

use Codificar\LaravelSubscriptionPlan\Models\Plan;
use Codificar\LaravelSubscriptionPlan\Models\Signature;
use Codificar\LaravelSubscriptionPlan\Models\Transaction;

use App\Http\Controllers\Controller;
use Provider;

class WebProviderController extends Controller {

	/**
	* Get all plans for providers and auth provider data
	*/
	public function listPlans() {
		$plans = Plan::where('client', 'Provider')->where('visibility', 1)->get();
		$provider = Provider::find(\Auth::guard("providers")->user()->id);
		$validSignature = [];
		$signature = $provider->signature;
		
		if ( $signature ) {
			$now = strtotime(date('Y-m-d H:i:s'));
			$signatureNextExpiration = strtotime($signature->next_expiration);
			$transactionSignature = Transaction::getSignatureTransaction($signature->id);
			
			$validSignature['isPaid'] = false;
			$validSignature['isPix'] = false;
			$validSignature['pix'] = array();
			$validSignature['status'] = 'empty';
			if($transactionSignature) {
				$validSignature['status'] = $transactionSignature->getStatus();
				$validSignature['isPaid'] = $transactionSignature->isPaid();
				$validSignature['isPix'] = $transactionSignature->pix_base64 ? true : false;
				
				$expiredPix = strtotime($transactionSignature->pix_expiration_date_time);
				$validSignature['pix']['isValid'] = false;
				$validSignature['pix']['transaction_id'] = $transactionSignature->id;
				if($now <= $expiredPix) {
					$validSignature['pix']['isValid'] = true;
				}
				
			}
			
			$validSignature['activity'] = filter_var($signature->activity, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

			if ($now <= $signatureNextExpiration) {
				$validSignature['is_valid'] = true;
				$validSignature['signature_id'] = $signature->id;
				$validSignature['next_expiration'] = date('Y-m-d H:i', $signatureNextExpiration);
				$validSignature['next_expiration_formated'] = date('d/m/Y H:i', $signatureNextExpiration);
				$validSignature['plan_id'] = $signature->plan_id;
			} else {
				$validSignature['is_valid'] = false;
				$validSignature['signature_id'] = $signature->id;
				$validSignature['plan_id'] = 0;	
			}
		}

		return view('subscriptionPlan::provider_panel.plan')
					->with('plans', $plans)
					->with('validSignature', json_encode($validSignature));
	}

	/** Get credits cards */
	public function listCards() {
		$provider = Provider::find(\Auth::guard("providers")->user()->id); 
		$payment = Payment::getPaymentsByProviderId($provider->id);

		return view('subscriptionPlan::provider_panel.credit_card')
					->with('cards', json_encode($payment))
					->with('providerId', $provider->id);
	}

}