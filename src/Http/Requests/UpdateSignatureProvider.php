<?php

namespace Codificar\LaravelSubscriptionPlan\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Provider, Payment;
use Codificar\LaravelSubscriptionPlan\Models\Plan;
use Carbon\Carbon;

class UpdateSignatureProvider extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'charge_type' => 'required|in:card,billet,gatewayPix',
            'plan_id'   => 'required',
            'payment_id'=> 'required_if:charge_type,card',
            'plan' 	    => 'required',
            'payment'   => 'required_if:charge_type,card'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->provider_id = $this->provider_id ?? $this->id;

        $this->plan = Plan::find($this->plan_id);
        $this->payment = Payment::find($this->payment_id);
        $this->provider = Provider::find($this->provider_id);

        if (!$this->payment || $this->payment->provider_id != $this->provider->id) {
            $this->payment = null;
        }

        $actualSubscription = $this->provider->signature;

        if ($actualSubscription && $actualSubscription->activity && !$actualSubscription->is_cancelled && $this->plan) {
            $nextExp = Carbon::parse($actualSubscription->next_expiration);
            $now = Carbon::now();
            $this->plan->period += $now->diffAsCarbonInterval($nextExp)->getHumanDiffOptions();
        }
        
        $this->merge([
            'plan' => $this->plan,
            'payment' => $this->payment
        ]);
    }

    /**
     * If the validation fails, it returns the error items
     * 
     * @return Json
     */
    protected function failedValidation(Validator $validator) 
    {   
        // Get error messages     
        $error_messages = $validator->errors()->all();

        // Displays error parameters
        throw new HttpResponseException(
        response()->json(
            [
                'success' => false,
                'error' => $error_messages[0],
                'error_code' => \ApiErrors::REQUEST_FAILED,
                'error_messages' => $error_messages,
            ]
        ));
    }
}