<?php

namespace App\Models\Interfaces;

interface DocumentInterface
{
	/**
     * Set directory name to save documents files into
     * 
     * @param string $directoryName
     * @return void
     */
	public function setStorageDirectory($directoryName);
	
    /**
     * Fetch directory name to save documents files
     * 
     * @param none
     * @return string
     */
	public function getStorageDirectory();
	
	/**
     * Create pdf
     * 
     * @param none
     * @return void
     */
	public function createPdf();
	
	/**
     * Create image from generated pdf
     * 
     * @param none
     * @return void
     */
	public function createThumbnail();
	
	/**
     * Delete pdf
     * 
     * @param none
     * @return void
     */
	public function deletePdf();
	
	/**
     * Delete generated pdf image
     * 
     * @param none
     * @return void
     */
	public function deleteThumbnail();
}
