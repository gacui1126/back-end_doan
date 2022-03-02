<?php

namespace App\Events;

use App\Task_details;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobListEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $jobDetail;
    public $taskDetail;
    public $event;
    public function __construct(Task_details $taskDetail,$jobDetail,$event)
    {
        $this->jobDetail = $jobDetail;
        $this->taskDetail = $taskDetail;
        $this->event = $event;
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
            'jobDetail' => $this->jobDetail,
            'event' => $this->event
        ];
    }
}
