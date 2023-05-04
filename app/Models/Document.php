<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Lib\Fpdf\PDF;

class Document extends Model
{
    use HasFactory, SoftDeletes;
	
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
	
	public function createPdf() {
		new PDF($this, true);
	}
	
	public function createPdfImage() {
		$imagick = new \Imagick();
		$imagick->readImage(storage_path().'\app\public\documents\pdf_'.$this->uuid.'.pdf[0]');
		$imagick->writeImage(storage_path().'\app\public\documents\image_'.$this->thumbnail);
	}
}
