<?php

namespace Codificar\LaravelSubscriptionPlan\Http\Requests;

use Codificar\LaravelSubscriptionPlan\Rules\CheckCancelSubscription;
use Codificar\LaravelSubscriptionPlan\Models\Signature;
use Provider;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class CancelSubscriptionRequest extends FormRequest
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
            'subscription' => ['required', new CheckCancelSubscription($this->plan, $this->subscription)]
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $provider = Provider::find($this->provider_id ?? $this->id);
        $subscription = Signature::find($this->subscription_id);

        if ($provider && $subscription && $provider->signature_id == $subscription->id) {
            
            $this->merge([
                'subscription' => $subscription,
                'plan' => $subscription->plan
            ]);
        }
    }

    /**
     * Retorna um json caso a validação falhe.
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $error_messages = $validator->errors()->all();
        
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'error' => $error_messages[0],
                'error_code' => \ApiErrors::REQUEST_FAILED,
                'error_messages' => $error_messages,
            ])
        );
    }
}
