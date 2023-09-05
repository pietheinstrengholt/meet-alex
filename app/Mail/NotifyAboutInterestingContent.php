<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyAboutInterestingContent extends Mailable
{
	use Queueable, SerializesModels;

	public $user;
	public $collection;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct($user, $collection)
	{
		$this->user = $user;
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
		->subject('Meet-Alex: Notification to inform you about some interesting content!')
		->view('email.sharecollection');
	}
}
