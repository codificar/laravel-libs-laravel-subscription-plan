<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class RunSubscriptionBilletEmailSeeder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $appName = Settings::findByKey('website_title')? Settings::findByKey('website_title') : '';

        EmailTemplate::updateOrCreate(['key' => 'subscription_billet'], [
            'key' => 'subscription_billet', 
            'subject' => trans('email.subscription_billet', ['app' => $appName]), 
            'copy_emails' => Settings::getAdminEmail(), 
            'from' => Settings::getAdminEmail()
        ]);
    
        $template = EmailTemplate::where('key', 'subscription_billet')->first();
    
        if ($template) {
            $template->content = trim('
                <div style="width: 100%;">
                <div style="margin: 0 auto; max-width: 640px; padding: 15px; text-align: center;"><img style="width: 100%; max-width: 320px; height: 170px;" src="{{ $vars["logo"] }}" alt="Logo da empresa" /></div>
                <div style="margin: 0 auto; max-width: 640px; padding: 15px; text-align: center;">
                <p style="font-size: 20px; font-family: "Roboto", sans-serif;">{{ $vars["billet_frase"] }}</p>
                <p style="font-size: 20px; font-family: "Roboto", sans-serif;">'. trans('email.access_billet_frase') .'</p>
                </div>
                <div style="margin: 0 auto; max-width: 640px; padding: 15px; text-align: center;"><a style="padding: 11px 16px; color: #fff; background-color: #28a745; text-decoration: none; border-radius: 5px;" href="{{ $vars["billet_url"] }}"> <span style="font-size: 20px; letter-spacing: 0.5px; font-weight: bold; font-family: "Roboto", sans-serif;">' . trans('email.access_billet') . '</span> </a></div>
                </div>
            ');
            $template->save();
        }

        EmailTemplate::updateOrCreate(['key' => 'subscription_billet_paid'], [
            'key' => 'subscription_billet_paid', 
            'subject' => trans('email.subscription_billet_paid', ['app' => $appName]), 
            'copy_emails' => Settings::getAdminEmail(), 
            'from' => Settings::getAdminEmail()
        ]);
    
        $template = EmailTemplate::where('key', 'subscription_billet_paid')->first();
    
        if ($template) {
            $template->content = trim('
                <div style="width: 100%;">
                <div style="margin: 0 auto; max-width: 640px; padding: 15px; text-align: center;"><img style="width: 100%; max-width: 320px; height: 170px;" src="{{ $vars["logo"] }}" alt="Logo da empresa" /></div>
                <div style="margin: 0 auto; max-width: 640px; padding: 15px; text-align: center;">
                <p style="font-size: 20px; font-family: "Roboto", sans-serif;">{{ $vars["billet_frase"] }}</p>
                </div>
                </div>
            ');
            $template->save();
        }

        EmailTemplate::updateOrCreate(['key' => 'subscription_billet_warning'], [
            'key' => 'subscription_billet_warning', 
            'subject' => trans('email.subscription_billet_warning', ['app' => $appName]), 
            'copy_emails' => Settings::getAdminEmail(), 
            'from' => Settings::getAdminEmail()
        ]);
    
        $template = EmailTemplate::where('key', 'subscription_billet_warning')->first();
    
        if ($template) {
            $template->content = trim('
                <div style="width: 100%;">
                <div style="margin: 0 auto; max-width: 640px; padding: 15px; text-align: center;"><img style="width: 100%; max-width: 320px; height: 170px;" src="{{ $vars["logo"] }}" alt="Logo da empresa" /></div>
                <div style="margin: 0 auto; max-width: 640px; padding: 15px; text-align: center;">
                <p style="font-size: 20px; font-family: "Roboto", sans-serif;">{{ $vars["billet_frase"] }}</p>
                </div>
                </div>
            ');
            $template->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
