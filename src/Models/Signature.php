<?php

namespace Codificar\LaravelSubscriptionPlan\Models;

use Illuminate\Support\Facades\DB;
use Codificar\LaravelSubscriptionPlan\Models\Settings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
use Codificar\LaravelSubscriptionPlan\Models\Plan, App\Models\Provider as Provider ;
class Signature extends Model
{
    //
    /**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'signature';
		
	protected $appends = ['next_expiration_formated', 'created_at_formated', 'good_to_cancel_date', 'is_expired'];
	
	/** 
	 * Function to get the next_expiration attribute formated
	 * 
	 * @return string
	 */
	public function getNextExpirationFormatedAttribute()
	{
		return date("d/m/Y H:i:s", strtotime($this->next_expiration));	
	}
	
	/** 
	 * Function to get the created_at_formated attribute formated
	 * 
	 * @return string
	 */
	public function getCreatedAtFormatedAttribute()
	{
		return date("d/m/Y H:i:s", strtotime($this->created_at));	
	}
	
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
    public $timestamps = true;
    
     /**
	 * Finds one row in the provider table associated with 'provider_id'
	 *
	 * @return Provider object
	 */
	public function provider()
	{
		return $this->belongsTo('Provider', 'provider_id', 'id');
    }
    
     /**
	 * Finds one row in the provider table associated with 'provider_id'
	 *
	 * @return PLan object
	 */
    public function plan()
	{
		return $this->belongsTo(Plan::class, 'plan_id', 'id');
	}
	
	/**
	 * Finds one row in the transaction table associated
	 *
	 * @return Finance object
	 */
	public function transaction()
	{
		return $this->belongsTo('Transaction', 'transaction_id', 'id');
	}
	
	/**
	 * Finds one row in the payment table associated
	 *
	 * @return Finance object
	 */
	public function payment()
	{
		return $this->belongsTo('Payment', 'payment_id', 'id');
    }
    
    /**
	 * Finds one row in the provider table associated with 'provider_id'
	 *
	 * @return Finance object
	 */
    public function finance()
	{
		return $this->belongsTo('Finance', 'finance_id', 'id');
	}
		
	public static function getActivatedSignature()
	{
		$return = Signature::where('activity', '=', 1)->get();

		return $return;
	}
		
	/**
	 * get provider signature active by provider_id
	 * @param int $providerId
	 * 
	 * @return Provider
	 */
	public static function getActiveProviderSignature($providerId)
	{
		$return = self::where(['activity' => 1])
			->where(['is_cancelled' => 0])
			->where(['provider_id' => $providerId])
			->first();

		return $return;
	}

	/**
	* Método que retorna a data de melhor dia de cancelamento
	* @return string
	*/
    public function getGoodToCancelDateAttribute()
    {		
        $goodToCancel = Carbon::parse($this->next_expiration);
        $goodToCancel = $goodToCancel->subDays(Settings::getBilletExpirationDays())->toDateString();
		return date('d/m/Y', strtotime($goodToCancel));
    }

	/**
	* Método se está ou não expirado
	* @return string
	*/
    public function getIsExpiredAttribute()
    {
		$now = strtotime(date('Y-m-d H:i:s'));
        $signatureNextExpiration = strtotime($this->next_expiration);
		return $now > $signatureNextExpiration;
    }

	/**
	 * get list of signatures
	 * 
	 * @return signature
	 */
	public static function getList(){

		$signature = Signature::leftJoin('plan', 'signature.plan_id', '=', 'plan.id')
		->leftJoin('provider', 'signature.provider_id', '=', 'provider.id')
		->groupBy('signature.id')
		->select('signature.id', 'plan.name as name', 'signature.created_at', 'signature.next_expiration', 'plan.plan_price as plan_price', 
		'provider.first_name as first_name', 'signature.activity', 'signature.is_cancelled')
		->orderBy('signature.id', 'desc')
		->paginate(10);

		return $signature;
	}

	/**
	 * get list of signatures by plan id
	 * 
	 * @param int $id
	 * @return signature
	 */
	public static function getListByPlanId($id){

		$signature = Signature::select(['signature.*'])
		->where(['plan_id' => $id])
		->get();

		return $signature;
	}

	/**
	 * get list of signatures by plan id and update data, used in case of plan deletion
	 * 
	 * @param int $id
	 * @return Signature
	 */
	public static function getListByPlanIdToDelete($id){

		$signature = Signature::where(['plan_id' => $id])
		->where(['is_cancelled' => 0])
		->where(['activity' => 1])
		->join('provider','signature.provider_id', '=', 'provider.id')
		->update([
			'signature.activity' => false,
			'signature.is_cancelled' => true,
			'signature.payment_id' => null,
			'provider.signature_id' => null
		]);

		return $signature;
	}

	/**
	 * get list of signatures by plan id and update data, used in case of plan deletion
	 * 
	 * @param $request
	 * @return array
	 */
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
		$query = Signature::WhereNotNull('signature.id');
		

		// get filters conditions
		$filters = isset($params["filters"]) ? $params["filters"] : $params;
		
		// field by field condition
		
		if(isset($filters["signatures"])){
			$signature = $filters["signatures"] ;

			if(isset($params["simplefilters"]) && $params["simplefilters"])
			{
				$query->leftJoin('plan', 'signature.plan_id', '=', 'plan.id')
				->leftJoin('provider', 'signature.provider_id', '=', 'provider.id')
				->where("signature.id", "=", 1)
				->orWhere("plan.name", "LIKE", "%".$signature["name"]."%")
				->orWhere("provider.first_name", "LIKE", "%".$signature["client_name"]."%")
				->orWhere("plan.plan_price", "LIKE", "%".$signature["signature_price"]."%")
				->orWhere("signature.created_at", "LIKE", "%".$signature["initial_date"]."%")
				->orWhere("signature.next_expiration", "LIKE", "%".$signature["expiration_date"]."%");
			} else {

				if(isset($signature["id"]) && $signature["id"]!= "")
					$query->where('id', '=', $signature["id"]);
				
				if(isset($signature["name"]) && $signature["name"]!= "")
					$query->join('plan', 'signature.plan_id', '=', 'plan.id')->where('plan.name', 'LIKE', '%'.$signature["name"].'%');
				
				if(isset($signature["client_name"]) && $signature["client_name"]!= "")
					$query->join('provider', 'signature.provider_id', '=', 'provider.id')
					->where('provider.first_name', 'LIKE', '%'.$signature["client_name"].'%');
						
				if(isset($signature["signature_price"]) && $signature["signature_price"]!= "")
					$query->join('plan', 'signature.plan_id', '=', 'plan.id')
					->where('plan.plan_price', 'LIKE', '%'.$signature["signature_price"].'%');
				
				if(isset($signature["initial_date"]) && $signature["initial_date"]!= "")
					$query->where('signature.created_at', 'LIKE', '%'.$signature["initial_date"].'%');

				if(isset($signature["expiration_date"]) && $signature["expiration_date"]!= "")
					$query->where('signature.next_expiration', 'LIKE', '%'.$signature["expiration_date"].'%');
							
			}

		}
		
		$query = $query->select(
				'signature.id as id', 
				'signature.finance_id', 
				'signature.activity', 
				'signature.transaction_id', 
				'signature.next_expiration', 
				'signature.created_at as signature_created_at', 
				'signature.updated_at as signature_updated_at', 
				'signature.charge_type', 
				'signature.is_cancelled', 
				'signature.payment_id', 
				'signature.check_billet_at', 
				'plan.id as plan_id', 
				'plan.name', 
				'plan.period', 
				'plan.plan_price', 
				'plan.client', 
				'plan.created_at as plan_created_at', 
				'plan.updated_at as plan_updated_at', 
				'plan.validity', 
				'plan.visibility', 
				'plan.location', 
				'plan.allow_cancelation',
				'provider.first_name', 
				'provider.id as provider_id')
			->leftjoin('plan', 'signature.plan_id', '=', 'plan.id')
			->leftjoin('provider', 'signature.provider_id', '=', 'provider.id')
			->groupBy('id')
			->orderBy('id', 'desc')
			->paginate($pagination["itensPerPage"]);

		$type = gettype($query);
		
		$response = [
			'signatures' => $query 
		];
		return $response;		
	}

	/**
	 * Make a new signature 
	 * 
	 * @param int $providerId
	 * @param int $planId
	 * @param Date    $nextExpiration
	 * @param int $financeId
	 * @param string $chargeType
	 */
	public static function updateProviderSignature($id, $providerId, $planId, $nextExpiration, $transactionId = null, $activity, $chargeType, $payment = null) {
		try {
			$signature = new Signature;

			if ($chargeType == 'billet' || $chargeType == 'gatewayPix') {
				$activity = 0;
			}
				
			$signature->provider_id = $providerId;
			$signature->plan_id = $planId;
			$signature->next_expiration  = $nextExpiration->toDateTimeString();
			$signature->finance_id  = null;
			$signature->transaction_id  = $transactionId;
			$signature->activity = $activity;
			$signature->charge_type = $chargeType;
			$signature->is_cancelled = false;

			if ($payment) {
				$signature->payment_id = $payment->id;
			}

			if ($chargeType == 'billet') {
				$checkAt = Carbon::now()->addDays(Settings::getDaysForSubscriptionRecurrency());
				$signature->check_billet_at = $checkAt->format('Y-m-d');
			}

			$signature->deactiveProviderSignatures($providerId);
			$signature->save();
			return $signature;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * Returns a signature record
	 * 
	 * @param int $transactionId
	 * @return array
	 */
	public static function getByTransactionId($transactionId)
	{
		return self::where('transaction_id', $transactionId)->first();
	}
	/**
	 * update other provider subscriptions to inactive
	 * 
	 * @param int $providerId
	 * @return array
	 */
	public static function deactiveProviderSignatures($providerId)
	{
		return self::where('provider_id', $providerId)
			->update(['activity' => 0]);
	}

	/**
	 * Get subscriptions payed by billet to check paid status
	 * 
	 * @return array
	 */
	public static function getWithBilletForCheckExpiry ()
	{
		return self::where('activity', 1)
			->where('charge_type', 'billet')
			->where('check_billet_at', '>=', Carbon::now()->format('Y-m-d'))
			->with(['transaction', 'provider'])
			->get();
	}

	/**
	 * Try to cancel a subscription
	 * 
	 * @return boolean
	 */
	public function cancel()
	{
		try {
			$this->payment_id = null;
			$this->is_cancelled = true;
			$this->activity = false;
			$this->save();
			return true;
		} catch (\Throwable $th) {
			\Log::error($th->getMessage());
			return false;
		}
	}

	/**
	 * Get subscriptions to recurrence
	 * 
	 * @return array
	 */
	public static function getSubscriptionsForRecurrency()
	{
		$days = Settings::getDaysForSubscriptionRecurrency();
		$expiration = Carbon::now()->addDays($days);
		
        return self::where('activity', 1)
			->where('next_expiration', $expiration->format('Y-m-d'))
			->with(['plan', 'payment', 'provider'])
            ->get();
	}

	
	/**
	 * Update all signature expired
	 * 
	 * @return array
	 */
	public static function updateSignatures()
	{
		$now = date('Y-m-d H:i:s');
		try {
			Signature::select(['signature.*'])
				->where('signature.next_expiration', '<', $now)
				->where(['signature.is_cancelled' => 0])
				->join('provider','signature.provider_id', '=', 'provider.id')
				->update([
					'signature.activity' => false,
					'signature.is_cancelled' => true,
					'signature.payment_id' => null,
					'provider.signature_id' => null
				]);
		} catch(\Exception $e) {
			\Log::error($e->getMessage() . $e->getTraceAsString());
		}
	}

}