<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Interfaces\DocumentInterface;
use App\Models\Traits\Documentable;

class Document extends Model implements DocumentInterface
{
    use HasFactory, SoftDeletes, Documentable;
	
	/**
     * Directory name.
     *
     * @var string
     */
	public $storageDirectory = "documents";
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'uuid',
		'name',
        'page_settings',
		'draggables',
		'thumbnail',
    ];
	
	/**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'deleted_at' => 'datetime',
        'page_settings' => 'array',
        'draggables' => 'array',
    ];
	
}
