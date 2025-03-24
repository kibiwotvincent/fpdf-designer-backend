<?php

namespace App\Http\Resources;

use App\Models\Subscription;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $userSubscribedPlanId = $request->user()->getSubscribedPlan(true);
        $freeSubscription = Subscription::free();

		return [
				'id' => $this->uuid,
				'title' => $this->title,
				'description' => $this->description,
                'price' => number_format($this->price, 2),
                'duration' => $this->duration,
                'duration_type' => $this->duration_type,
                'stripe_name' => $this->stripe_name,
                'stripe_price_id' => $this->stripe_price_id,
                'items' => $this->items,
                'deleted' => $this->trashed(),
                'is_active' => $this->uuid == $userSubscribedPlanId,
                'is_free' => $this->uuid == $freeSubscription?->uuid
        ];
    }
	
}
