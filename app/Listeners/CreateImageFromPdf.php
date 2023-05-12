<?php

namespace App\Listeners;

use App\Events\DocumentSaved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;

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
		$document = $event->document;
		//create directory if it does not exist
		Storage::makeDirectory('public/'.$document->storageDirectory);
        
		//first create pdf
		$document->createPdf();
		
		//then create thumbnail from pdf
		$document->createThumbnail();
    }
}
