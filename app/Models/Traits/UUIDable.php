<?php

namespace App\Models\Traits;

trait UUIDable
{
	/**
     * Generate a uuid to be used in a model
     * 
     * @param none
     * @return string
     */
	public static function uuid() {
		return md5(time().rand(111111, 999999));
	}
}
