<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use App\Term;
use App\User;

class TermChanged extends Event
{
    use SerializesModels;

    public $term;
    public $user;

    public function __construct(Term $term, User $user)
    {
        $this->term = $term;
        $this->user = $user;
    }
}
