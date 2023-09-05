<!-- /resources/views/terms/editmodal.blade.php -->

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Edit the term "{{ $term->term_name }}"</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

<!-- -->

		@section('content')

			@include('tinymce.term')

			@if (count($errors) > 0)
				<div class="alert alert-danger">
				<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
				</ul>
				</div>
			@endif

			{!! Form::model($term, ['method' => 'PATCH', 'route' => ['collections.terms.update', $collection->id, $term->id]]) !!}
		 	@include('terms/partials/_form', ['submit_text' => 'Update Term', 'propose_text' => 'Propose Term'])
			{!! Form::close() !!}

		@endsection

 <!-- -->

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
