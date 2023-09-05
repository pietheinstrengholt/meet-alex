<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Request;
use Response;
use Log;

class WebHookController extends Controller
{
	public function webhook()
	{
		try {
			$xEvent = Request::header('X-GitHub-Event');
			$payload = json_decode(Request::getContent());
		} catch(Exception $e) {
			Log::info('Error Handling Webhook content');
			return;
		}

		//Check if it's a push event, just in case we register for all events.
		if ($xEvent !='push') {
			Log::info('Ignoring X-GitHub-Event' .$xEvent );
			return Response::json(['message'=>'ignored non push event'], 200);
		}

		//Check if it's a push to the master branch.
		if ($payload->ref !='refs/heads/master') {
			Log::info('Ignoring push on branch' .$payload->ref);
			return Response::json(['message'=>'ignored push to branch :' .$payload->ref ], 200);

		}

		//Log
		Log::info('Github Webhook Push Event fired');
		Log::info('Deploying new code because of a commit push by ' . $payload->head_commit->author->name);
		Log::info('Deploying commit ID : ' . $payload->after);

		// really only need to use this if you have ngrok running and testing the webhook code.
		if (\App::environment('local'))
		{
			Log::info('Skipping github webhook on local machine');
		}

		//Perform deployment
		Log::info('Performing deployment');
		Log::info(shell_exec('cd /var/www/ && sudo /var/www/deploy-dev.sh 2>&1'));

		return Response::json(['message'=>'processing push event deploying updates, thanks'], 200);
	}
}
