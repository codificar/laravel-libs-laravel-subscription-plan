<?php
namespace Codificar\LaravelSubscriptionPlan\Http\Controllers;

use Codificar\LaravelSubscriptionPlan\Models\Plan;
use Codificar\LaravelSubscriptionPlan\Jobs\NewSubscriptionMail;
use Codificar\LaravelSubscriptionPlan\Http\Requests\UpdateSignatureProvider;
use Codificar\LaravelSubscriptionPlan\Http\Resources\UpdatePlanResource;

use Carbon\Carbon;
use Codificar\LaravelSubscriptionPlan\Models\Signature;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

use Provider;
use Auth;
use Payment;
use PaymentFactory;
use Location;

class PlanController extends Controller
{
    /**
     * Renders the payment screen for the chosen plan
     * 
     * @param $id id do plano(parâmetro de rota)
     * @return view checkout_plan.blade.php
     */
    public function checkoutPlan($id) {
        $plan = Plan::find($id);
        $provider = Provider::find(Auth::guard("providers")->user()->id); 
        $payment = Payment::getPaymentsByProviderId($provider->id);

        \Log::info($payment);

        if (!$plan) 
            return redirect('/provider/plans');   

        return view('subscriptionPlan::provider_panel.checkout_plan')
                    ->with('plan', $plan)
                    ->with('provider', $provider)
                    ->with('payment', $payment);
    }

    /**
     * Return view to add plane after getting locations without polygon area
     * 
     * @return view checkout_plan.blade.php
     */
	public function add(){
        $locations = Location::allOrderByAlphaUnsetPoligons();
        
        return view('subscriptionPlan::plan.add', ['edit' => false, 'plan' => '', 'locations' => $locations]);	
    }
    
    /**
     * Save plan data in the table
     * 
     * @param Request $request
     * @return redirect
     */
    public function store(Request $request){
        try {
            $values = \Input::get('plan');
    
            if(array_key_exists('id', $values) && !empty($values['id']))
                $plan = Plan::find($values['id']);
            else
                $plan = new Plan;
    
            $plan->name = $values['name'];
            $plan->period = $values['period'];
            $plan->plan_price = $values['plan_price'];
            $plan->client = $values['client'];
            $plan->validity = $values['validity'];
            $plan->visibility = $values['visibility'];
            $plan->location = $values['location'];
            $plan->allow_cancelation = $values['allow_cancelation'];
            $plan->save();
            
            return Redirect::to('admin/plan/list', 201);
        } catch(\Exception $e) {
            \Log::error($e->getMessage() . $e->getTraceAsString());
            return Redirect::to('admin/plan/add', 400);
        }
    }

    /**
     * Generates a list of plans and returns
     * 
     * @return view
     */
    public function index(){
        $plans = Plan::getList();
        $jsonPlans = json_encode($plans);
        $currency = \Settings::findByKey('generic_keywords_currency');
        
        return view('subscriptionPlan::plan.list', ['plans' => $jsonPlans, 'currency' => $currency]); 
    }

     /**
	 * Finds a row in the provider table associated with 'provider_id' 
     * get ledger by user id
     * 
     * @param Request $request
     * @return querry
     */
    public function query(Request $request){
        $model = new Plan;
        $query = $model->querySearch($request);
		return $query;
    }
    
    /**
	 * Deletes the plan and makes the necessary modifications to the provider and subscription tables
     * 
     * @param $id 
     * @return string
     */
    public function destroy($id){
        $plan = Plan::find($id);
        if ($plan){
            try {
                Signature::getListByPlanIdToDelete($id);
                $plan->delete();
                return "success";
            }catch (\Exception $e) {
                return "failed";
            }
        }else{
            return "failed";
        }
    }

    /**
     * Returns the list of available plans for the provider
     * 
     * @param $id 
     * @return view
     */
    public function show($id){
        $plan = Plan::find($id);

        $locations = Location::allOrderByAlpha();

        if ($locations)
            $locations = $locations->each(function ($item, $key) {
                unset($item['area_polygon']);
            });

        return view('subscriptionPlan::plan.add', ['edit' => true, 'plan' => $plan, 'locations' => $locations]);
    }

    /**
     * Returns the list of available plans for the provider
     * 
     * @param Request $request
     * @return json
     */
    public function getPlansListForProvider (Request $request) {
        $plans = [];
        $provider = Provider::find($request->id);

        if($provider)
            $plans = Plan::getPlansListForProvider($provider->location_id, $provider->id);

        return response()->json(['success' => true, 'plans' => $plans]);
    }

}
