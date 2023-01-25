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
     * Renderiza a tela de pagamento do plano escolhido
     * 
     * @param id id do plano(parâmetro de rota)
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

	public function add(){
        $locations = Location::allOrderByAlpha();

        if ($locations)
            $locations = $locations->each(function ($item, $key) {
                unset($item['area_polygon']);
            });
        
        return view('subscriptionPlan::plan.add', ['edit' => false, 'plan' => '', 'locations' => $locations]);	
    }
    
    public function store(Request $request){
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
        
        return Redirect::to('admin/plan/list');
    }

    public function index(){
        $plans = Plan::getList();
        $jsonPlans = json_encode($plans);
        return view('subscriptionPlan::plan.list', ['plans'=>$jsonPlans]); 
    }

    public function query(Request $request){
        $model = new Plan;
        $query = $model->querySearch($request);
		return $query;
    }
    
    public function destroy($id){
        $plan = Plan::find($id);
        if ($plan){
            try {
                $list = Signature::getListByPlanId($id);
                foreach ($list as $key => $list) {
                    if ($list->activity && !$list->is_cancelled ){
                        $list->cancel();
                        $provider = Provider::find($list->provider_id);
                        $provider->updateSignatureId(null);
                    }
                }
                $plan->delete();
                return "success";
            }catch (\Exception $e) {
                return "failed";
            }
        }else{
            return "failed";
        }
    }

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
     * Retorna a lista de planos disponíveis para o provider
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
