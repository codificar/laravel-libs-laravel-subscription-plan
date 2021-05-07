<?php
//rotas do Institution Controller
Route::group(array('namespace'=>'Codificar\LaravelSubscriptionPlan\Http\Controllers'), function () {
    /**
    * Rotas com Middleware
    */
    Route::group(['prefix' => 'api/v3/admin','middleware' => ['auth.admin_api', 'cors'],], function(){
        Route::get('/plan', 'PlanController@index');
        Route::post('/storePlan', 'PlanController@store');
		Route::get('/plan/add', 'PlanController@add');
		Route::get('/plan/filter', 'PlanController@filter');
		Route::post('/plan/delete', 'PlanController@delete');
		Route::post('/plan/update', 'PlanController@addOrUpdate');
		Route::get('/plan/signatures', 'SignatureController@index');
    });
    
    Route::group(['prefix' => 'admin','middleware' => ['auth.admin_api', 'cors'],], function(){

        Route::get('/plan/add', array('as' => 'AddPlan', 'uses' => 'PlanController@add'));
        Route::post('/plan/store', array('as' => 'StorePlan', 'uses' => 'PlanController@store'));
        Route::get('/plan', array('as' => 'PlanList', 'uses' => 'PlanController@index'));
        Route::get('/plan/list', array('as' => 'PlanList', 'uses' => 'PlanController@index'));
        Route::get('/plan/filter', array('as' => 'FilterPlan', 'uses' => 'PlanController@filter'));
        Route::post('/plan/fetch', array('as' => 'PlanFetch', 'uses' => 'PlanController@query'));
        Route::post('/plan/delete/{id}', array('as' => 'PlanDelete', 'uses' => 'PlanController@destroy'));
        Route::get('/plan/{id}', array('as' => 'PlanShow', 'uses' => 'PlanController@show'));

        Route::get('/signature', array('as' => 'SignatureList', 'uses' => 'SignatureController@list'));
        Route::post('/signature/fetch', array('as' => 'PlanFetch', 'uses' => 'SignatureController@query'));
        Route::post('/signature/suspend/{id}', array('as' => 'SuspendOrActivate', 'uses' => 'SignatureController@suspendOrActivate'));
    });

    /**
     * Routes for provider panel
     */  
    Route::group(['prefix' => 'libs/provider', 'middleware' => 'auth.provider'], function(){
        Route::get('/plans', array('as' => 'ProviderListPlans','uses' => 'WebProviderController@listPlans'));
        Route::get('/get_plans', array('as' => 'GetPlans','uses' => 'WebProviderController@getPlans'));
        Route::get('/credit_card', array('as' => 'ListCreditCard','uses' => 'WebProviderController@listCards'));
        Route::post('/plan/updatePlan', array('as' => 'UpdatePlan', 'uses' => 'SignatureController@newProviderSubscription'));
        Route::get('/plan/{id}', array('as' => 'CheckoutPlan', 'uses' => 'PlanController@checkoutPlan'));
    });


    Route::get('/provider_plans', array('uses' => 'PlanController@getPlansListForProvider'));

    /**
     * Rotas para aplicativo android provider
     */
    Route::group(['prefix' => 'api/libs/provider' ,'middleware' => 'auth.provider_api:api'], function() {
        /**
         * @OA\Post(path="/api/v3/provider/update_plan",
         *      tags={"Provider"},
         *      operationId="NewSubscription",
         *      description="Cria uma nova assinatura",
         *      @OA\Parameter(
         *          name="id",
         *          description="Id do usuário",
         *          in="query",
         *          required=true,
         *          @OA\Schema(type="integer")
         *      ),
         *      @OA\Parameter(
         *          name="token",
         *          description="Token de autenticação do usuário",
         *          in="query",
         *          required=true,
         *          @OA\Schema(type="string")
         *      ),
         *      @OA\Parameter(
         *          name="plan_id",
         *          description="ID do plano",
         *          in="query",
         *          required=true,
         *          @OA\Schema(type="integer")
         *      ),
         *      @OA\Parameter(
         *          name="payment_id",
         *          description="ID do cartão de crédito",
         *          in="query",
         *          required=false,
         *          @OA\Schema(type="integer")
         *      ),
         *      @OA\Parameter(
         *          name="charge_type",
         *          description="Método de pagamneto billet ou card",
         *          in="query",
         *          required=true,
         *          @OA\Schema(type="integer")
         *      ),
         *      @OA\Response(
         *          response="200",
         *          description="Retorna se a assinatura foi bem sucedida",
         *          @OA\JsonContent(ref="#/components/schemas/UpdatePlanResource")
         *      ),
         *      @OA\Response(
         *          response="402",
         *          description="Erro na validação dos dados enviados."
         *      ),
         * )
         */
        Route::post('/update_plan', array('uses' => 'SignatureController@newProviderSubscription'));

        /**
         * @OA\Get(path="/api/v3/provider/subscription_details",
         *      tags={"Provider"},
         *      operationId="SubscriptionDetails",
         *      description="Retorna os detalhes de uma assinatura",
         *      @OA\Parameter(
         *          name="id",
         *          description="Id do usuário",
         *          in="query",
         *          required=true,
         *          @OA\Schema(type="integer")
         *      ),
         *      @OA\Response(
         *          response="200",
         *          description="Retorna os detalhes de uma assinatura",
         *          @OA\JsonContent(ref="#/components/schemas/SubscriptionDetailResource")
         *      ),
         *      @OA\Response(
         *          response="402",
         *          description="Erro na validação dos dados enviados."
         *      ),
         * )
         */
        Route::get('/subscription_details', array('uses' => 'SignatureController@getDetails'));

        /**
         * @OA\Post(path="/api/v3/provider/cancel_subscription",
         *      tags={"Provider"},
         *      operationId="CancelSignature",
         *      description="Cancela assinatura",
         *      @OA\Parameter(
         *          name="id",
         *          description="Id do usuário",
         *          in="query",
         *          required=true,
         *          @OA\Schema(type="integer")
         *      ),
         *      @OA\Parameter(
         *          name="token",
         *          description="Token de autenticação do usuário",
         *          in="query",
         *          required=true,
         *          @OA\Schema(type="string")
         *      ),
         *      @OA\Parameter(
         *          name="subscription_id",
         *          description="ID da assinatura",
         *          in="query",
         *          required=true,
         *          @OA\Schema(type="integer")
         *      ),
         *      @OA\Response(
         *          response="200",
         *          description="Retorna se a assinatura foi bem sucedida"
         *      ),
         *      @OA\Response(
         *          response="402",
         *          description="Erro na validação dos dados enviados."
         *      ),
         * )
         */
        Route::post('/cancel_subscription', array('uses' => 'SignatureController@cancelSubscription'));

        Route::get('/plans', array('uses' => 'PlanController@getPlansListForProvider'));
    });
});

/** Credit card */
Route::group(['prefix' => 'api/v3', 'middleware' => ['auth.admin_api', 'cors']], function(){
    Route::post('/addCard', array('uses' => 'CreditCardController@addPaymentCard'));
    Route::post('/defaultCard', array('uses' => 'CreditCardController@defaultCard'));
    Route::post('/removeCard', array('uses' => 'CreditCardController@removeCard'));
});

Route::group(['middleware' => 'auth.admin'], function() {
    Route::get('/admin/test_pay_status/{id}', 'api\v3\SignatureController@testBilletPayment');
});

/**
 * Rota para permitir utilizar arquivos de traducao do laravel (dessa lib) no vue js
 */
Route::get('/libs/signature/lang.trans/{file}', function () {
    $fileNames = explode(',', Request::segment(4));
    $lang = config('app.locale');
    $files = array();
    foreach ($fileNames as $fileName) {
        array_push($files, __DIR__.'/../resources/lang/' . $lang . '/' . $fileName . '.php');
    }
    $strings = [];
    foreach ($files as $file) {
        $name = basename($file, '.php');
        $strings[$name] = require $file;
    }

    header('Content-Type: text/javascript');
    return ('window.lang = ' . json_encode($strings) . ';');
    exit();
})->name('assets.lang');
