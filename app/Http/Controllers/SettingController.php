<?php

namespace App\Http\Controllers;
use App\Setting;
use App\Http\Controllers\Controller;
use App\User;
use Gate;
use Illuminate\Http\Request;
use Redirect;

class SettingController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	 public function index()
	 {
		//check for admin permissions
		if (Gate::denies('admin')) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//rotate array in order to process it better
		$config_array = array();

		$settings = Setting::orderBy('config_key', 'asc')->get();

		if (!empty($settings)) {
			foreach($settings as $setting) {
				$config_key = $setting['config_key'];
				$config_array[$config_key] = $setting['config_value'];
			}
		}

		return view('settings.index', compact('config_array','scanned_img_directory','scanned_css_directory'));
	}

	public function store(Request $request)
	{
		//check for admin permissions
		if (Gate::denies('admin')) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//validate input form
		$this->validate($request, [
			'main_message1' => 'required',
			'main_message2' => 'required',
			'administrator_email' => 'required|email',
			'approve_own_changes' => 'required',
			'enable_groups' => 'required',
		]);

		//truncate table
		Setting::truncate();

		foreach ($request->except(['_token']) as $key => $value) {
			$setting = new Setting;
			$setting->config_key = $key;
			$setting->config_value = $value;
			$setting->save();
		}

		return Redirect::to('/')->withErrors(['Settings updated']);
	}
}
