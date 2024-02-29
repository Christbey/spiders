<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConfigurationCompleted
{
    use Dispatchable, SerializesModels;

    public $configuration;

    public function __construct($configuration)
    {
        $this->configuration = $configuration;
    }



    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
   /* public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];

    }*/
}
