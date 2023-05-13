<?php

namespace App\Facades;

use App\Lib\Fpdf\FpdfAdapter;

class PDF
{
    /**
     * Dynamically call the fpdf adapter instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
		$pdf = new FpdfAdapter(...$parameters);
        return $pdf->{$method}();
    }
}
