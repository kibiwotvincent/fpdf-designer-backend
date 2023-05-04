<?php

namespace App\Listeners;

use App\Events\DocumentSaved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Log;

class CreateImageFromPdf implements ShouldQueue
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
     * @param  \App\Events\DocumentSaved  $event
     * @return void
     */
    public function handle(DocumentSaved $event)
    {
		Log::info('document saved');
		Storage::makeDirectory('public/documents');
        $document = $event->document;
		//first create pdf
		$document->createPdf();
		//then create thumbnail from pdf
		$document->createPdfImage();
    }
}
