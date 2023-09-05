<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyUserAboutTermLinkChange extends Mailable
{
	use Queueable, SerializesModels;

	public $user;
	public $term;
	public $collection;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	 public function __construct($user, $term, $collection)
 	{
 		$this->user = $user;
 		$this->term = $term;
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
		->subject('Meet-Alex: Notification that a term has been changed!')
		->view('email.termchanged');
	}
}
