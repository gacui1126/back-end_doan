<?php

namespace App\Events;

use App\Jobs;
use App\Task_details;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class jobEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $job;
    public $taskDetail;
    public $event;
    public function __construct(Task_details $taskDetail,$job,$event)
    {
        $this->job = $job;
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
        $this->job->job_details = [];
        $this->job->addJobFormShow = true;
        $this->job->addJobForm = false;
        return  [
            'job' => $this->job,
            'event' => $this->event
        ];
    }
}
