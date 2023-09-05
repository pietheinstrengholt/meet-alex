<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use App\User;
use Auth;

class LoginController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles authenticating users for the application and
	| redirecting them to your home screen. The controller uses a trait
	| to conveniently provide its functionality to your applications.
	|
	*/

	use AuthenticatesUsers;

	/**
	 * Where to redirect users after login.
	 *
	 * @var string
	 */
	 protected $redirectTo = '/collections';
	 protected $redirectAfterLogout = '/';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest', ['except' => 'logout']);
	}

	/**
	* Redirect the user to the OAuth Provider.
	*
	* @return Response
	*/
	public function redirectToProvider($provider)
	{
		return Socialite::driver($provider)->redirect();
	}

	/**
	* Obtain the user information from provider.  Check if the user already exists in our
	* database by looking up their provider_id in the database.
	* If the user exists, log them in. Otherwise, create a new user then log them in. After that
	* redirect them to the authenticated users homepage.
	*
	* @return Response
	*/
	public function handleProviderCallback($provider)
	{
		$user = Socialite::driver($provider)->user();

		$authUser = $this->findOrCreateUser($user, $provider);
		Auth::login($authUser, true);
		return redirect($this->redirectTo);
	}

	/**
	* If a user has registered before using social auth, return the user
	* else, create a new user object.
	* @param  $user Socialite user object
	* @param $provider Social auth provider
	* @return  User
	*/
	public function findOrCreateUser($user, $provider)
	{
		if ($provider) {
			//check if the twitter email address is not blank
			if (empty($user->email)) {
				if ($provider == "twitter") {
					abort(400, '400 Bad Request. The \'Request email addresses from users\' checkbox is not selected under the app permissions on apps.twitter.com.');
				} else {
					abort(400, '400 Bad Request. The Social user\'s email address cannot be empty!');
				}
			}

			//abort if the user's name is not set (github allows empty personal names as an example)
			if (empty($user->name)) {
				abort(400, '400 Bad Request. The Social user\'s name cannot be empty!');
			}

			//check if user already exists using a non-OAuth authentication method
			$existingUser = User::withTrashed()->where('email', $user->email)->whereNull('provider')->first();
			if ($existingUser) {
				abort(400, '400 Bad Request. The Social user\'s email address is already registered in the database using a non-OAuth authentication method. Please use the email and password form to sign in.');
			}

			//check if user already exists with an email adress from a different oauth provider
			$existingoAuth = User::withTrashed()->where('provider', '<>', $provider)->where('email', $user->email)->first();
			if ($existingoAuth) {
				abort(400, '400 Bad Request. The Social user\'s email address is already registered in the database using different OAuth provider. Please use the email account from the different oAuth provider.');
			}

			//check if user is already registered
			$authUser = User::withTrashed()->where('provider', $provider)->where('provider_id', $user->id)->first();
			if ($authUser) {

				//update database entries for name/email when user uses oauth again
				User::withTrashed()->where('provider', $provider)->where('provider_id', $user->id)->update(['name' => $user->name, 'email' => $user->email]);

				//restore user object if trashed
				if ($authUser->trashed()) {
					$authUser->restore();
				}

				//return oauth object
				return $authUser;
			}
		}
		return User::create([
			'name'	 => $user->name,
			'email'	=> $user->email,
			'provider' => $provider,
			'provider_id' => $user->id
		]);
	}
}
