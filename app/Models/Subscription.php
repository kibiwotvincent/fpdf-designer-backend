<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\UUIDable;

class Subscription extends Model
{
    use HasFactory, SoftDeletes, UUIDable;
    
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
}
