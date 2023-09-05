<?php

namespace App\Listeners;

use App\Events\UserBookmarkedCollection;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Mail\Mailer;
use Mail;
use App\Mail\NotifyOwnerAboutCollectionBookmark;

class EmailOwnerAboutCollectionBookmark
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
	 * @param  UserBookmarkedCollection  $event
	 * @return void
	 */
	public function handle(UserBookmarkedCollection $event)
	{
		// Access the collection using $event->collection...
		// Access the user using $event->user...

		//create variables
		$user = $event->user;
		$owner = $event->collection->owner;
		$collection = $event->collection;

		Mail::to($event->collection->owner->email, $event->collection->owner->name)->send(new NotifyOwnerAboutCollectionBookmark($user, $owner, $collection));
	}
}
