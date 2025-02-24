<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable;

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
        'subscription_expires_at' => 'datetime',
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
     * Determine if the user has an active subscription.
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
            //check if subscription has expired and has exceeded allowed monthly requests
            if($this->subscriptionHasExpired() && $this->maxRequestsExceeded()) {
                return false;
            }
        }

        return true;
    }

    public function subscriptionHasExpired() {
        $now = Carbon::now();
        return $this->subscription_expires_at != "" &&  $now->gt($this->subscription_expires_at);
    }

    /**
     * Determine if the user has an active subscription.
     *
     * @return bool
     */
    public function isOnFreePlan() {
        return $this->subscription_expires_at == "";
    }

    /**
     * Determine if the user has an active subscription.
     *
     * @return bool
     */
    public function maxRequestsExceeded() {
        $currentMonth = date('m');
        $requestsThisMonth = $this->apiRequests()->whereMonth('created_at', $currentMonth)->count();
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

	
}
