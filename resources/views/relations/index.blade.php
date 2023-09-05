<!-- /resources/views/relations/index.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
	<li><a href="{!! url('/'); !!}">Home</a></li>
	<li><a href="{!! route('collections.index'); !!}">Collections</a></li>
	<li><a href="{{ route('collections.show', $collection) }}">{{ $collection->collection_name }}</a></li>
	<li class="active">Relations</li>

	@if (Auth::check())
		<li class="right dropdown" id="status"><div style="font-size: 18px; margin-top: -3px; float:left; margin-left: 3px;"><a href="{!! route('collections.relations.create', $collection->id); !!}"><span title="Click to create a new Collection" class="btn btn-info btn-xs">Create</span></a></div></li>
	@endif
	</ul>

	@if ( !$relations->count() )
		No relations found in the database. Use to button in the menu to create a new database!<br><br>
	@else
		<table class="table section-table dialog table-striped" border="1">

		<tr class="info">
		<td class="header">Relation Name</td>
		<td class="header">Description</td>
		<td class="header" style="width: 120px;">Options</td>
		</tr>

		@foreach( $relations as $relation )
			<tr>
			<td>{{ strtolower($relation->relation_name) }}</td>
			<td>{{ $relation->relation_description }}</td>
			{!! Form::open(array('class' => 'form-inline', 'method' => 'DELETE', 'route' => array('collections.relations.destroy', $collection->id, $relation->id), 'onsubmit' => 'return confirm(\'Are you sure to delete this relation type?\')')) !!}
			<td>
			{!! link_to_route('collections.relations.edit', 'Edit', array($collection->id, $relation->id), array('class' => 'btn btn-info btn-xs')) !!}
			{!! Form::submit('Delete', array('class' => 'btn btn-danger btn-xs', 'style' => 'margin-left:3px;')) !!}
			</td>
			{!! Form::close() !!}
			</tr>
		@endforeach

		</table>
	@endif
@endsection
