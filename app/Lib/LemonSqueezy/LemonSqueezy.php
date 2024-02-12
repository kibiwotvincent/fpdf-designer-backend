<?php

namespace App\Lib\LemonSqueezy;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class LemonSqueezy
{
    const VERSION = '1.4.0';

    const API = 'https://api.lemonsqueezy.com/v1';

    
    /**
     * Perform a Lemon Squeezy API call.
     *
     * @throws Exception
     * @throws LemonSqueezyApiError
     */
    public static function stores()
    {
        if (empty($apiKey = config('lemon-squeezy.api_key'))) {
            throw new Exception('Lemon Squeezy API key not set.');
        }

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withToken($apiKey)
            ->withUserAgent('LemonSqueezy\Laravel/'.static::VERSION)
            ->accept('application/vnd.api+json')
            ->contentType('application/vnd.api+json')
            ->get(static::API."/stores");

        return $response;
    }
    
    public static function createCustomer()
    {
        if (empty($apiKey = config('lemon-squeezy.api_key'))) {
            throw new Exception('Lemon Squeezy API key not set.');
        }
        $storeID = config('lemon-squeezy.store');
        $customer = [
                        'type' => "customers",
                        'attributes' => [
                            'name' => "Vincent Kibiwot",
                            'email' => "vinkib2@example.com",
                        ],
                        'relationships' => [
                             'store' => [
                                  "data" => [
                                      'type' => "stores",
                                      'id' => $storeID
                                  ]
                              ]
                        ]
                    ];
        
        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withToken($apiKey)
            ->withUserAgent('LemonSqueezy\Laravel/'.static::VERSION)
            ->accept('application/vnd.api+json')
            ->contentType('application/vnd.api+json')
            ->post(static::API."/customers", ['data' => $customer]);

        return $response;
    }
    
    public static function subscriptions()
    {
        if (empty($apiKey = config('lemon-squeezy.api_key'))) {
            throw new Exception('Lemon Squeezy API key not set.');
        }

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withToken($apiKey)
            ->withUserAgent('LemonSqueezy\Laravel/'.static::VERSION)
            ->accept('application/vnd.api+json')
            ->contentType('application/vnd.api+json')
            ->get(static::API."/subscriptions");

        return $response;
    }
}
