<?php

namespace App\Events;

use App\Tags;
use App\Task_details;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TagEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $tag;
    public $taskDetail;

    public function __construct(Task_details $taskDetail,Tags $tag)
    {
        $this->taskDetail = $taskDetail;
        $this->tag = $tag;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('taskdetail.'.$this->taskDetail->id);
    }

    public function broadcastWith()
    {
        return  [
            'tag' => $this->tag
        ];
    }
}
