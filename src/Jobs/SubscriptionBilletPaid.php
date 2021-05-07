<?php

namespace Codificar\LaravelSubscriptionPlan\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Theme, Provider, EmailTemplate;
use Codificar\LaravelSubscriptionPlan\Models\Plan;
use Codificar\LaravelSubscriptionPlan\Models\Signature;

class SubscriptionBilletPaid implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $signature;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Signature $signature)
    {
        $this->signature = $signature;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->signature->activity = 1;
        $this->signature->save();

        $provider    = Provider::find($this->signature->provider_id); 
        $plan        = Plan::find($this->signature->plan_id);
        $template    = EmailTemplate::where('key', 'subscription_billet_paid')->first(); 

        $vars = [
            'logo' => url(Theme::getLogoUrl()),
            'billet_frase' => trans('email.billet_mail_paid', ['name' => $provider->getFullName(), 'plan_name' => $plan->name])
        ];
        
        email_notification($this->signature->provider_id, 'provider', $vars, $template->subject, 'subscription_billet_paid');
    }
}
