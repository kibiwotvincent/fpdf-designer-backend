<?php

namespace App\Lib\LemonSqueezy\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Log;

/**
 * @internal Not supported by any backwards compatibility promise. Please use events to react to webhooks.
 */
final class WebhookController extends Controller
{
    public function __construct()
    {
       // if (config('lemon-squeezy.signing_secret')) {
            //$this->middleware(VerifyWebhookSignature::class);
       // }
    }

    /**
     * Handle a Lemon Squeezy webhook call.
     */
    public function __invoke(Request $request)
    {
        $payload = $request->all();
        
        Log::info($payload);
    }
}