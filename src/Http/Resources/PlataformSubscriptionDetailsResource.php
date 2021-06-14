<?php

namespace Codificar\LaravelSubscriptionPlan\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Codificar\LaravelSubscriptionPlan\Models\Settings;

/**
 * Class PlataformSubscriptionDetailsResource
 *
 * @package UberClone
 *
 * @OA\Schema(
 *     	schema="PlataformSubscriptionDetailsResource",
 *     	type="object",
 *     	description="Response for new subscription",
 *     	title="Provider Review Resource",
 *		allOf={
 *       	@OA\Schema(ref="#/components/schemas/PlataformSubscriptionDetailsResource"),
 *       	@OA\Schema(
 *          	required={"success"},
 *           	@OA\Property(property="success", format="boolean", type="boolean"),
 *           	@OA\Property(property="required_plan", format="string", type="string"),
 * 				@OA\Property(property="review", ref="#/components/schemas/PlataformSubscriptionDetailsResource")
 *       	)
 *   	}
 * )
 */
class PlataformSubscriptionDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $plan = $this['plan'];

        return [
            'success' => true,
            'required_plan' => $plan
        ];
    }
}
