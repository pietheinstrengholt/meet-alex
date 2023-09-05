<!-- /resources/views/errors/excel.blade.php -->
@extends('layouts.app')

@section('content')

	<h2>Whoops! It looks like something went wrong when importing the Excel file</h2>
	<h4>Please review the errors below carefully. Correct the Excel and please try again.</h4>

	@if (count($errors) > 0)
		<div class="alert alert-danger">
		<ul>
		@foreach ($errors as $error)
			<li>{{ $error }}</li>
		@endforeach
		</ul>
		</div>
	@endif

	@if (empty($alexArray['terms']) || empty($alexArray['ontologies']))
		Error: One of the sheets is missing from the Excel. Please validate the Excel file and try again.
	@else
		@if (!empty($alexArray['terms']))
			<strong>Terms sheet</strong>
			<table class="table table-bordered template" style="width:70%" border="1">
			<tr class="success">
			<td class="header">id</td>
			<td class="header">term_name</td>
			<td class="header">term_definition</td>
			</tr>

			@foreach($alexArray['terms'] as $key => $row)
				@if (array_key_exists('error', $row))
					<tr class="error" style="background-color: #e74c3c; color: #ffffff;">
				@else
					<tr>
				@endif
				<td>{{ $key }}</td>
				<td>{{ $row['term_name'] }}</td>
				@if (isset($row['term_definition']))
					<td>{{ $row['term_definition'] }}</td>
				@else
					<td></td>
				@endif
				</tr>
			@endforeach
			</table>
		@endif

		@if (!empty($alexArray['ontologies']))
			<strong>Terms sheet</strong>
			<table class="table table-bordered template" style="width:70%" border="1">
			<tr class="success">
			<td class="header">subject_id</td>
			<td class="header">relation_id</td>
			<td class="header">object_id</td>
			</tr>

			@foreach($alexArray['ontologies'] as $row)
				@if (array_key_exists('error', $row))
					<tr class="error" style="background-color: #e74c3c; color: #ffffff;">
				@else
					<tr>
				@endif
				@if (isset($row['subject_id']))
					<td>{{ $alexArray['terms'][$row['subject_id']]['term_name'] }}</td>
				@else
					<td></td>
				@endif
				@if (isset($row['relation_id']))
					<td>{{ $alexArray['relations'][$row['relation_id']]['relation_name'] }}</td>
				@else
					<td></td>
				@endif
				@if (isset($row['object_id']))
					<td>{{ $alexArray['terms'][$row['object_id']]['term_name'] }}</td>
				@else
					<td></td>
				@endif
				</tr>
			@endforeach
			</table>
		@endif
	@endif

	<p>
		<a href="{{ url('uploadexcel') }}">Return to Excel import</a>
	</p>

@endsection
