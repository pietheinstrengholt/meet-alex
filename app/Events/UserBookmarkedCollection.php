<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Collection;
use App\User;

class UserBookmarkedCollection extends Event
{
    use SerializesModels;

    public $collection;
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Collection $collection, User $user)
    {
        $this->collection = $collection;
        $this->user = $user;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
