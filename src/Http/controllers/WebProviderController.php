<?php
namespace Codificar\LaravelSubscriptionPlan\Http\Controllers;

use Codificar\LaravelSubscriptionPlan\Models\Plan;
use Codificar\LaravelSubscriptionPlan\Models\Signature;

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
		$actualSignature = $provider->signature_id;
		
		if ( $actualSignature ) {
			$signature = subscriptionPlan::find($actualSignature);
			$now = strtotime(date('Y-m-d'));
			$signatureNextExpiration = strtotime($signature->next_expiration);

			if ($now <= $signatureNextExpiration) {
				$validSignature['is_valid'] = true;
				$validSignature['signature_id'] = $signature->id;
				$validSignature['next_expiration'] = date('d/m/Y', $signatureNextExpiration);
				$validSignature['plan_id'] = $signature->plan_id;
			} else {
				$validSignature['is_valid'] = false;
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