<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiRequest extends Model
{
    use HasFactory;
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'api_key',
		'ip_address',
        'document_id',
        'subscription_plan_id',
    ];

    public function plan() 
	{
		return $this->hasOne('App\Models\Subscription'::class, 'id', 'subscription_plan_id');
	}

    /**
	 * Query scope to only include requests from free plan.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $query
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeViaFreePlan($query)
	{
        $freePlanId = Subscription::free()->id;
		return $query->where('subscription_plan_id',  $freePlanId);
	}
	
}
