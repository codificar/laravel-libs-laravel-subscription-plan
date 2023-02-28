<?php

namespace Codificar\LaravelSubscriptionPlan\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Codificar\LaravelSubscriptionPlan\Models\Signature;

use Provider;

class SubscriptionDetailsFormRequest extends FormRequest
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
            'signature' => 'required',
            'transaction' => 'required',
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

        $this->provider = Provider::find($this->provider_id);
        $this->signature = $this->provider->signature;
        if(!$this->signature) {
            $this->signature = Signature::find($this->provider->signature_id);
        }
        $this->transaction = null;

        if ($this->signature)
            $this->transaction = $this->signature->transaction;
       
        // Send the data to the request
        $this->merge([
            'signature' => $this->signature,
            'transaction' => $this->transaction
        ]);
	}

    /**
     * Returns a json if validation fails
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
	 * 
     * @return Json {'success','errors','error_code'}
     *
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success'       => false,
                'errors'        => $validator->errors()->all(),
                'error_code'    => \ApiErrors::REQUEST_FAILED
            ])
        );
    }
}
