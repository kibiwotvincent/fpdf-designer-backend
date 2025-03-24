<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\UUIDable;

class Subscription extends Model
{
    use HasFactory, SoftDeletes, UUIDable;
    
    protected $table = 'subscription_plans';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'title',
        'price',
        'description',
        'items',
        'duration',
        'duration_type',
        'stripe_name',
        'stripe_price_id',
    ];
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
		'items' => 'array',
        'deleted_at' => 'datetime',
    ];

    public static function free() {
        //get subscription plan which is the `free plan`
        return self::where('price', 0)->first();
    }
}
