<?php
namespace Codificar\LaravelSubscriptionPlan;

use Codificar\LaravelSubscriptionPlan\Console\Commands\UpdateSignaturesJob;
use Illuminate\Support\ServiceProvider;

class SubscriptionPlanServiceProvider extends ServiceProvider {

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/subscriptionPlan.php');

        $this->loadViewsFrom(__DIR__.'/resources/views', 'subscriptionPlan');

        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');

        // Load trans files (Carrega tos arquivos de traducao) 
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'subscriptionPlanTrans');

        // Publish the VueJS files inside public folder of main project (Copia os arquivos do vue minificados dessa biblioteca para pasta public do projeto que instalar essa lib)
        $this->publishes([
            __DIR__.'/../public/js' => public_path('vendor/codificar/subscription-plan/js'),
        ], 'public_vuejs_libs');

        // Publish the tests files 
        $this->publishes([
            __DIR__ . '/../tests/' => base_path('tests/Unit/libs/plan'),
        ], 'publishes_tests');

        
		if ($this->app->runningInConsole()) {
			$this->commands([
				UpdateSignaturesJob::class
			]);
		}
    }

    public function register()
    {

    }
}
?>