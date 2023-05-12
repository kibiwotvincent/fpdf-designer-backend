<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Interfaces\DocumentInterface;

class DocumentDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
	
	/**
	 * The document instance.
	 *
	 * @var \App\Models\Document
	 */
	public $document;

    /**
     * Create a new event instance.
     * 
     * @param \App\Models\Document $document
     * @return void
     */
    public function __construct(DocumentInterface $document)
    {
        $this->document = $document;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        //return new PrivateChannel('channel-name');
    }
}
