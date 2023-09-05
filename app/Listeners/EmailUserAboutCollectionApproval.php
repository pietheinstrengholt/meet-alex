<?php

namespace App\Listeners;

use App\Events\OwnerApprovedCollectionRights;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Mail\Mailer;
use Mail;
use App\Mail\NotifyUserAboutCollectionApproval;

class EmailUserAboutCollectionApproval
{
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct(Mailer $mailer)
	{
		$this->mailer = $mailer;
	}

	/**
	 * Handle the event.
	 *
	 * @param  OwnerApprovedCollectionRights  $event
	 * @return void
	 */
	public function handle(OwnerApprovedCollectionRights $event)
	{
		// Access the collection using $event->collection...
		// Access the user using $event->user...

		//create variables
		$user = $event->user;
		$collection = $event->collection;

		Mail::to($event->collection->owner->email, $event->collection->owner->name)->send(new NotifyUserAboutCollectionApproval($user, $collection));
	}
}
