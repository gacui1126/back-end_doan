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

class ReplyCommentEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $comment;
    public $taskDetail;
    public $event;

    public function __construct(Task_details $taskDetail, $comment, $event)
    {
        $this->taskDetail = $taskDetail;
        $this->comment = $comment;
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
        $this->comment->editComment = false;
        $this->comment->showComment = true;
        $this->comment->showRepply = false;
        $this->comment->Edit = false;
        $this->comment->OEdit = true;
        return  [
            'comment' => $this->comment,
            'event' => $this->event
        ];
    }
}
