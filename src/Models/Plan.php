<?php

namespace Codificar\LaravelSubscriptionPlan\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Http\Request;
use Codificar\LaravelSubscriptionPlan\Models\ignature;
use Illuminate\Pagination\Paginator;
use Schema, DB, Provider;

class Plan extends Model
{
    //
    /**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'plan';
	
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
    public $timestamps = true;
    
    /**
	 * Finds one row in the provider table associated with 'provider_id'
	 * get Ledger by User Id
	 * @return Signature | null
	 **/
	public function signature()
	{
		return $this->hasMany('App\Models\Signature', 'plan_id', 'id');
	}

	public function querySearch(Request $request)
	{
		// get query parameters
		$params = $request->all();

		// get pagination conditions
		if(isset($params["pagination"])) {
			$pagination = $params["pagination"];
		}
		else { // set default 
			$pagination =  ["actual" => 1, "itensPerPage" => 25 ] ;
		}

		// resolve current page
		$currentPage = $pagination["actual"];
		Paginator::currentPageResolver(function () use ($currentPage) {
			return $currentPage;
		});

		// set first condition 1=1 (all results)
		$query = Plan::WhereNotNull('plan.id');

		// get filters conditions
		$filters = isset($params["filters"]) ? $params["filters"] : $params;
		
		// field by field condition
				
		if(isset($filters["Plan"])){
			$PlanConditions = $filters["Plan"] ;

			if(isset($params["simpleFilter"]) && $params["simpleFilter"])
			{
				$query->join('signature', 'plan.id', '=', 'signature.plan_id')
				->where("plan.id", "=", $PlanConditions["id"])
				->orWhere("plan.name", "LIKE", "%".$PlanConditions["name"]."%")
				->orWhere("plan.plan_price", "LIKE", "%".$PlanConditions["plan_price"]."%")
				->orWhere("plan.validity", "LIKE", "%".$PlanConditions["validity"]."%");
			} else {
				// field by field condition
						
			if(isset($PlanConditions["id"]) && $PlanConditions["id"]!= "")
				$query->leftJoin('signature', 'plan.id', '=', 'signature.plan_id')
				->where('id', '=', $PlanConditions["id"]);

			if(isset($PlanConditions["name"]) && $PlanConditions["name"]!= "")
				$query->leftJoin('signature', 'plan.id', '=', 'signature.plan_id')
				->where('plan.name', 'LIKE', '%'.$PlanConditions["name"].'%');

			if(isset($PlanConditions["plan_price"]) && $PlanConditions["plan_price"]!= "")
				$query->leftJoin('signature', 'plan.id', '=', 'signature.plan_id')
				->where('plan.plan_price', 'LIKE', '%'.$PlanConditions["plan_price"].'%');
		
			if(isset($PlanConditions["validity"]) && $PlanConditions["validity"]!= "")
				$query->leftJoin('signature', 'plan.id', '=', 'signature.plan_id')
				->where('plan.validity', 'LIKE', '%'.$PlanConditions["validity"].'%');
						
			}

		}
		$qtds = array();
		$query = $query->select('*', DB::raw('ifnull(COUNT(plan_id), 0) as quantity'))
						->groupBy('plan.id')
						->paginate($pagination["itensPerPage"]);

		$type = gettype($query);

		$response = [
			'plans' => $query
		];
		return $response;
	}

	public static function getPlanValue($id)
	{
		$return =  Plan::where('id', '=', $id)->value('plan_price');
		return $return;
	}

	public static function getPlanById($signatureId)
	{
		return Plan::where('id', '=', $id)->get();
	}

	public static function getList(){

		$signature = DB::table('plan')
		->leftJoin('signature', 'plan.id', '=', 'signature.plan_id')
		->leftJoin('location', 'plan.location', '=', 'location.id')
		->select('plan.id', 'plan.name', 'plan.plan_price', 'plan.period', 'plan.validity', DB::raw('ifnull(COUNT(plan_id), 0) as quantity'), 'location.name as location_name')
		// ->select('plan.id', 'plan.name', 'plan.plan_price', 'plan.period')
		->groupBy('plan.id')
		->orderBy('signature.id')
		->paginate(10);

		return $signature;
	}

	/**
     * Retorna a lista de planos disponíveis para o provider
     */
	public static function getPlansListForProvider ($locationId)
	{
		$plans = self::where('client', 'Provider')->where('visibility', 1)->get();

		$return = [];

		for ($i=0; $i < count($plans); $i++) { 
			$plans[$i]->plan_price = currency_format($plans[$i]->plan_price);

			if ($plans[$i]->location != null && $locationId != $plans[$i]->location)
				continue;

			array_push($return, $plans[$i]);
		}
		
		return $return;
	}

	/**
	 * Retorna um plano para teste
	 */
	public static function createForTest()
	{
		$plan = self::where('name', 'Plano para teste')->first();

		if ($plan)
			return $plan;

		$plan = new Plan;
		$plan->name = 'Plano para teste';
		$plan->period = 30;
		$plan->plan_price = 30;
		$plan->client = 'Provider';
		$plan->validity = 30;
		$plan->visibility = 1;
		$plan->save();

		return $plan;
	}
}