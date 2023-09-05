<?php
namespace App\Http\Requests;

class CreateTermRequest extends Request
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'term_name' => 'required',
			'collection_id' => 'required',
			'status_id' => 'required',
			'owner_id' => 'required'
		];
	}
}
