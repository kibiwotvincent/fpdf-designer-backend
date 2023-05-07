<?php

namespace App\Listeners;

use App\Events\DocumentFilesDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Document;

class PermanentlyDeleteDocument implements ShouldQueue
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
     * @param  \App\Events\DocumentFilesDeleted  $event
     * @return void
     */
    public function handle(DocumentFilesDeleted $event)
    {
        $document = $event->document;
		$document->forceDelete();
    }
}
