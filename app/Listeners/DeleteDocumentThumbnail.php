<?php

namespace App\Listeners;

use App\Events\UpdatingDocument;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteDocumentThumbnail implements ShouldQueue
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
     * @param  \App\Events\UpdatingDocument  $event
     * @return void
     */
    public function handle(UpdatingDocument $event)
    {
        $document = $event->document;
		$document->deleteThumbnail();
    }
}
