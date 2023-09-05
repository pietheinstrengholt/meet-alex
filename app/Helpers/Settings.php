<?php

namespace app\Helpers;
use App\Helpers\Settings;
use App\Setting;

class Settings
{
	public static function get($input) {
		$setting = Setting::where('config_key', $input)->first();
		if (!empty($setting)) {
			return $setting->config_value;
		}
	}
}

?>
