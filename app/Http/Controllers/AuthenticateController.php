<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use Validator;

class AuthenticateController extends Controller
{
	public function login(Request $request)
	{
		// Validate user input
		$validator = Validator::make($request->all(), [
			'email' => 'required|email|max:255',
			'password' => 'required|min:6'
		]);

		if ($validator->fails()) {
			return response()->json($validator->errors(), 422);
		}

		// grab credentials from the request
		$credentials = $request->only('email', 'password');

		try {
			// attempt to verify the credentials and create a token for the user
			if (! $token = JWTAuth::attempt($credentials)) {
				return response()->json(['error' => 'invalid_credentials'], 401);
			}
		} catch (JWTException $e) {
			// something went wrong whilst attempting to encode the token
			return response()->json(['error' => 'could_not_create_token'], 500);
		}

		// all good so return the token
		return response()->json(compact('token'));
	}

	public function register(Request $request)
	{
		// Validate user input
		$validator = Validator::make($request->all(), [
			'name' => 'required|max:255',
			'email' => 'required|email|max:255|unique:users',
			'password' => 'required|min:6'
		]);

		if ($validator->fails()) {
			return response()->json($validator->errors(), 422);
		}

		$authUser = User::create([
			'name'	 => $request->input('name'),
			'email'	=> $request->input('name'),
			'password' => bcrypt($request->input('password'))
		]);

		return response()->json(['status'=>'success', 'user'=>$authUser], 200);
	}
}
