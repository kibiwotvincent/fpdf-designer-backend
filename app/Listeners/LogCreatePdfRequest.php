<?php

namespace App\Listeners;

use App\Events\CreatePdfRequestReceived;
use App\Models\ApiRequest;
use App\Models\Document;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Crypt;

class LogCreatePdfRequest implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\CreatePdfRequestReceived  $event
     * @return void
     */
    public function handle(CreatePdfRequestReceived $event)
    {
        $request = $event->request;
        $document = Document::where('uuid', $request['document_id'])->first();
        $user = User::where('api_key', $request['api_key'])->first();

        ApiRequest::create([
            'user_id' => $user->id,
            'api_key' => Crypt::encryptString($request['api_key']),
            'ip_address' => $request['ip_address'],
            'document_id' => $document->id,
            'subscription_plan_id' => $user->getSubscribedPlan(true)
        ]);
    }
}
