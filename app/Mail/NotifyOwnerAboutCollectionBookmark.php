<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyOwnerAboutCollectionBookmark extends Mailable
{
	use Queueable, SerializesModels;

	public $user;
	public $owner;
	public $collection;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct($user, $owner, $collection)
	{
		$this->user = $user;
		$this->owner = $owner;
		$this->collection = $collection;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this->from('admin@meet-alex.org', 'meet-Alex')
		->subject('Meet-Alex: Notification that an user has bookmarked your collection!')
		->view('email.userbookmarked');
	}
}
