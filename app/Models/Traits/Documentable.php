<?php

namespace App\Models\Traits;

trait Documentable
{
	/**
     * Set directory name to save documents files into
     * 
     * @param string $directoryName
     * @return void
     */
	public function setStorageDirectory($directoryName) {
		$this->storageDirectory = $directoryName;
	}
	
    /**
     * Fetch directory name to save documents files
     * 
     * @param none
     * @return string
     */
	public function getStorageDirectory() {
		return $this->storageDirectory;
	}
	
	/**
     * Create pdf
     * 
     * @param none
     * @return void
     */
	public function previewPdf() {
		\App\Facades\PDF::preview($this);
	}
	
	/**
     * Create pdf
     * 
     * @param none
     * @return void
     */
	public function createPdf() {
		\App\Facades\PDF::save($this);
	}
	
	/**
     * Create image from generated pdf
     * 
     * @param none
     * @return void
     */
	public function createThumbnail() {
		$imagick = new \Imagick();
		$imagick->readImage(storage_path().'/app/public/'.$this->storageDirectory.'/'.$this->uuid.'.pdf[0]');
		$imagick->writeImage(storage_path().'/app/public/'.$this->storageDirectory.'/'.$this->thumbnail);
	}
	
	/**
     * Delete pdf
     * 
     * @param none
     * @return void
     */
	public function deletePdf() {
		\Illuminate\Support\Facades\Storage::delete('/public/'.$this->storageDirectory.'/'.$this->uuid.'.pdf');
	}
	
	/**
     * Delete generated pdf image
     * 
     * @param none
     * @return void
     */
	public function deleteThumbnail() {
		\Illuminate\Support\Facades\Storage::delete('/public/'.$this->storageDirectory.'/'.$this->thumbnail);
	}
}
