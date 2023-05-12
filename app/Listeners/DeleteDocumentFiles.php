<?php

namespace App\Listeners;

use App\Events\DocumentDeleted;
use App\Events\DocumentFilesDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteDocumentFiles implements ShouldQueue
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
     * @param  \App\Events\DocumentDeleted  $event
     * @return void
     */
    public function handle(DocumentDeleted $event)
    {
        $document = $event->document;
		$document->deletePdf();
		$document->deleteThumbnail();
		DocumentFilesDeleted::dispatch($document);
    }
}
