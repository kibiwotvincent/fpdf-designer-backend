<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Lib\Fpdf\PDF;
use Illuminate\Support\Facades\Storage;

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
	
	/**
     * Creates document pdf and saves it in server.
     * 
     * @param none
     * @return void
     */
	public function createPdf() {
		new PDF($this, true);
	}
	
	/**
     * Creates image from the first page of saved pdf document and saves it in server.
     * 
     * @param none
     * @return void
     */
	public function createPdfImage() {
		$imagick = new \Imagick();
		$imagick->readImage(storage_path().'/app/public/documents/'.$this->uuid.'.pdf[0]');
		$imagick->writeImage(storage_path().'/app/public/documents/'.$this->thumbnail);
	}
	
	/**
     * Deletes document files from server.
     * 
     * @param none
     * @return void
     */
	public function deleteDocumentFiles() {
		$this->deletePdf();
		$this->deleteThumbnail();
	}
	
	/**
     * Deletes saved pdf from server.
     * 
     * @param none
     * @return void
     */
	public function deletePdf() {
		Storage::delete('/public/documents/'.$this->uuid.'.pdf');
	}
	
	/**
     * Deletes saved thumbnail from server.
     * 
     * @param none
     * @return void
     */
	public function deleteThumbnail() {
		Storage::delete('/public/documents/'.$this->thumbnail);
	}
}
