<?php

namespace Codificar\LaravelSubscriptionPlan\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Codificar\LaravelSubscriptionPlan\Models\Settings;

/**
 * Class SubscriptionDetailResource
 *
 * @package UberClone
 *
 * @OA\Schema(
 *     	schema="SubscriptionDetailResource",
 *     	type="object",
 *     	description="Response for new subscription",
 *     	title="Provider Review Resource",
 *		allOf={
 *       	@OA\Schema(ref="#/components/schemas/SubscriptionDetailResource"),
 *       	@OA\Schema(
 *          	required={"success"},
 *           	@OA\Property(property="success", format="boolean", type="boolean"),
 *           	@OA\Property(property="activity", format="string", type="string"),
 *           	@OA\Property(property="next_expiration", format="string", type="string"),
 *           	@OA\Property(property="plan_name", format="string", type="string"),
 *           	@OA\Property(property="paid_status", format="string", type="string"),
 *           	@OA\Property(property="billet_link", format="string", type="string"),
 * 				@OA\Property(property="review", ref="#/components/schemas/SubscriptionDetailResource")
 *       	)
 *   	}
 * )
 */
class SubscriptionDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'success' => true,
            'id' => $this['signature']->id,
            'activity' => $this['signature']['activity'],
            'next_expiration' => date('d/m/Y H:i', strtotime($this['signature']['next_expiration'])),
            'good_cancel_date' => $this['signature']['good_to_cancel_date'],
            'plan_name' => $this['signature']->plan->name,
            'is_active' => $this['signature']->activity,
            'is_cancelled' => $this['signature']->is_cancelled,
            'is_pix' => $this['signature']->charge_type == 'gatewayPix',
            'transaction_db_id' => $this['transaction']->id,
            'paid_status' => $this['transaction']->status,
            'billet_link' => $this['transaction']->billet_link,
            'expired' => $this['signature']['is_expired']
        ];
    }
}
