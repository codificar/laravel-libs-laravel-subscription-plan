<?php

namespace Codificar\LaravelSubscriptionPlan\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UpdatePlanResource
 *
 * @package UberClone
 *
 * @OA\Schema(
 *     	schema="UpdatePlanResource",
 *     	type="object",
 *     	description="Response for new subscription",
 *     	title="Provider Review Resource",
 *		allOf={
 *       	@OA\Schema(ref="#/components/schemas/UpdatePlanResource"),
 *       	@OA\Schema(
 *          	required={"success", "message"},
 *           	@OA\Property(property="success", format="boolean", type="boolean"),
 *           	@OA\Property(property="message", format="string", type="string"),
 * 				@OA\Property(property="review", ref="#/components/schemas/UpdatePlanResource")
 *       	)
 *   	}
 * )
 */
class UpdatePlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource;
    }
}
