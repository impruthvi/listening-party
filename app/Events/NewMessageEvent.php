<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewMessageEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $listeningPartyId;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct($listeningPartyId, $message)
    {
        $this->listeningPartyId = $listeningPartyId;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        Log::info("test", [
            $this->listeningPartyId,
            $this->message
        ]);
        return [
            new Channel('listening-party', $this->listeningPartyId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new-message';
    }
}
