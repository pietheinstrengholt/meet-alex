<?php

namespace App\Listeners;

use App\Events\TermChanged;
use DB;
use App\Term;
use App\Collection;
use Illuminate\Contracts\Mail\Mailer;
use Mail;
use App\Mail\NotifyUserAboutTermLinkChange;

class EmailUserAboutTermLinkChange
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

	public function handle(TermChanged $event)
	{
		// Access the user using $event->user...

		//find all changed terms in many to many table
		$changedTerms = DB::table('collection_term')->where('term_id', $event->term->id)->get();

		//find all collections that the term is linked to
		foreach ($changedTerms as $key => $changedTerm) {
			$collection = Collection::find($changedTerm->collection_id);
			if (!empty($collection)) {
				if ($collection->owner) {
					// Access the collection using $event->collection...
					// Access the user using $event->user...

					//create data object for sending email
					$data = $collection->owner;

					//create variables
					$user = $event->user;
					$term = $event->term;
					$collection = $collection;

					Mail::to($data->email, $data->name)->send(new NotifyUserAboutTermLinkChange($user, $term, $collection));
				}
			}
		}
	}
}
