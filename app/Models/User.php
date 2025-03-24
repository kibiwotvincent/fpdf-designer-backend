<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_key',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
	
	public function documents() 
	{
		return $this->hasMany('App\Models\Document'::class);
	}

    public function apiRequests() 
	{
		return $this->hasMany('App\Models\ApiRequest'::class);
	}

    /**
     * Generate API key.
     *
     * @return bool
     */
    public function generateApiKey() {
        $key = md5(time().$this->email);
        $this->api_key = $key;
        return $this->save();
    }

    /**
     * Determine if the user has an active subscription whether free or paid.
     *
     * @return bool
     */
    public function isSubscribed() {
        if($this->isOnFreePlan()) {
            //check if user has exceeded maximum requests allowed this month
            if($this->maxRequestsExceeded()) {
                return false;
            }
        } 
        else {
            //check if user is not subscribed and has exceeded allowed monthly requests
            if(!$this->subscribed('default') && $this->maxRequestsExceeded()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the user is on free plan.
     *
     * @return bool
     */
    public function isOnFreePlan() {
        return !$this->subscribed('default');
    }

    /**
     * Check if user has exceeded allowed free monthly requests.
     *
     * @return bool
     */
    public function maxRequestsExceeded() {
        $currentMonth = date('m');
        $requestsThisMonth = $this->apiRequests()->viaFreePlan()->whereMonth('created_at', $currentMonth)->count();
        return ($requestsThisMonth > 100);
    }

    /**
     * Check if user does not have an active subscription.
     *
     * @return bool
     */
    public function notSubscribedReason() {
        if($this->isOnFreePlan()) {
            $reason = "You have exhausted maximum requests allowed for free this month. Upgrade to premium plan.";
        }
        else {
            $reason = "Your subscription has expired and you have exceeded maximum requests allowed this month for free. Please renew your subscription.";
        }

        return $reason;
    }

    public function getSubscribedPlan($idOnly = false) {
        if($this->isOnFreePlan()) {
            $plan = Subscription::free();
        }

        else {
            $subscription = $this->subscription('default');
            if($subscription) {
                $priceId = $subscription->stripe_price;
                $plan = Subscription::where('stripe_price_id', $priceId)->first();
            }
        }

        return $idOnly ? $plan->uuid : $plan;
    }
	
}
