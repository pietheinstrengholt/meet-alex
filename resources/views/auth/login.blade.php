@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Login</div>
				<div class="panel-body">
					<form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
						{{ csrf_field() }}

						<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
							<label for="email" class="col-md-4 control-label">E-Mail Address</label>

							<div class="col-md-6">
								<input id="email" type="email" class="form-control" autofocus="autofocus" name="email" value="{{ old('email') }}">

								@if ($errors->has('email'))
									<span class="help-block">
										<strong>{{ $errors->first('email') }}</strong>
									</span>
								@endif
							</div>
						</div>

						<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
							<label for="password" class="col-md-4 control-label">Password</label>

							<div class="col-md-6">
								<input id="password" type="password" class="form-control" name="password">

								@if ($errors->has('password'))
									<span class="help-block">
										<strong>{{ $errors->first('password') }}</strong>
									</span>
								@endif
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="remember"> Remember Me
									</label>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-8 col-md-offset-4">
								<button type="submit" class="btn btn-primary">
									<i class="fa fa-btn fa-sign-in"></i> Login
								</button>

								<a class="btn btn-link" href="{{ url('/password/reset') }}">Forgot Your Password?</a>
							</div>
						</div>

						@if (getenv("GITHUB_ID") || getenv("TWITTER_ID") || getenv("FACEBOOK_ID") || getenv("LINKEDIN_ID"))
							<hr>
							<div class="form-group">
								<div class="col-md-8 col-md-offset-4">
									@if (getenv("GITHUB_ID"))
										<a href="{{ url('/auth/github') }}" class="btn btn-github btn-default btn-sm"><i class="fa fa-github"></i> Github</a>
									@endif
									@if (getenv("TWITTER_ID"))
										<a href="{{ url('/auth/twitter') }}" class="btn btn-twitter btn-default btn-sm"><i class="fa fa-twitter"></i> Twitter</a>
									@endif
									@if (getenv("FACEBOOK_ID"))
										<a href="{{ url('/auth/facebook') }}" class="btn btn-facebook btn-default btn-sm"><i class="fa fa-facebook"></i> Facebook</a>
									@endif
									@if (getenv("LINKEDIN_ID"))
										<a href="{{ url('/auth/linkedin') }}" class="btn btn-linkedin btn-default btn-sm"><i class="fa fa-linkedin"></i> LinkedIn</a>
									@endif
								</div>
							</div>
						@endif
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
