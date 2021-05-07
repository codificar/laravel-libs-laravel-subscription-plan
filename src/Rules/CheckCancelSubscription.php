<?php

namespace Codificar\LaravelSubscriptionPlan\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class CheckCancelSubscription implements Rule
{
    public $plan;
    public $subscription;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($plan, $subscription)
    {
        $this->plan = $plan;
        $this->subscription = $subscription;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $exp = Carbon::parse($this->subscription->next_expiration);
        
        if (!$this->plan->allow_cancelation && $exp->greaterThan(Carbon::now()))
            return false;

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('plan.allow_cancelation_error');
    }
}
