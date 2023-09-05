@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">Register</div>
				<div class="panel-body">
					<form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
						{{ csrf_field() }}

						<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
							<label for="name" class="col-md-4 control-label">Name</label>

							<div class="col-md-6">
								<input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Enter full name">

								@if ($errors->has('name'))
									<span class="help-block">
										<strong>{{ $errors->first('name') }}</strong>
									</span>
								@endif
							</div>
						</div>

						<div class="form-group{{ $errors->has('nickname') ? ' has-error' : '' }}">
							<label for="nickname" class="col-md-4 control-label">Nickname</label>

							<div class="col-md-6">
								<input id="nickname" type="text" class="form-control" name="nickname" value="{{ old('nickname') }}" placeholder="If you don't want your name to be exposed enter a nick name">

								@if ($errors->has('nickname'))
									<span class="help-block">
										<strong>{{ $errors->first('nickname') }}</strong>
									</span>
								@endif
							</div>
						</div>

						<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
							<label for="email" class="col-md-4 control-label">E-Mail Address</label>

							<div class="col-md-6">
								<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Enter your email address">

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
								<input id="password" type="password" class="form-control" name="password" placeholder="Enter your password">

								@if ($errors->has('password'))
									<span class="help-block">
										<strong>{{ $errors->first('password') }}</strong>
									</span>
								@endif
							</div>
						</div>

						<div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
							<label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

							<div class="col-md-6">
								<input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Repeat your password">

								@if ($errors->has('password_confirmation'))
									<span class="help-block">
										<strong>{{ $errors->first('password_confirmation') }}</strong>
									</span>
								@endif
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Terms of use</label>
							<div class="col-md-6">
								<div style="border: 1px solid #e5e5e5; height: 200px; overflow: auto; padding: 10px;">
									<p>These Website Standard Terms and Conditions (these “Terms” or these “Website Standard Terms and Conditions”) contained here on this webpage, shall govern your use of this website.</p>
									<p>These Terms apply in full force and effect to your use of this Website and by using this Site you accept all terms and conditions in full. You must not use https://www.meet-alex.org, if you have any objection to any of these Website Standard Terms and Conditions.</p>
									<p>Other than content you own, which you may have chosen to include on this Website, Meet-Alex and/or its licensors own all rights to the intellectual property and material contained in this Website. You are granted a limited license only, subject to the restrictions provided in these Terms, for purposes of viewing the material contained on https://www.meet-alex.org.</p>
									<p>Meet-Alex makes no express or implied warranties or representations of any kind with regards to this Website or the materials contained on this Website. In addition no content contained on this Website shall be considered as providing advice to you.</p>
									<p>Under no circumstances shall Meet-Alex, or any of its officers, directors and employees, be liable to you for anything resulting from or connected to your use of this Website. Meet-Alex, including its officers, directors and employees shall not be liable for any indirect, consequential or special liability resulting from or in any way related to your use of this Website.</p>
									<p>These Terms may be revised at any time Meet-Alex sees fit, and you are expected to review such terms on a regular basis to ensure your understanding of all terms and conditions governing use of this Website.revise the updated date at the bottom of this page. By using this Website you are acknowledging your responsibility to do so.</p>
									<p>These Terms, including any legal notices and disclaimers contained on this Website, constitute the entire agreement between Meet-Alex and you with regards to your use of this Website, and replace all prior agreements and understandings with respect to the same.</p>
									<p>These Terms will be governed by and construed in accordance with the laws of The Netherlands, and you submit to the nonexclusive jurisdiction of the courts located in The Netherlands for the resolution of any disputes.</p>
								</div>
							</div>
						</div>

						<div class="form-group has-feedback">
							<div class="col-xs-4 col-xs-offset-4">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="agree" value="agree" data-fv-field="agree"> Agree with the terms and conditions
									</label>
								</div><i class="form-control-feedback" data-fv-icon-for="agree" style="display: none;"></i>
								@if ($errors->has('agree'))
									<div class="alert alert-danger" style="margin-top: 5px; padding: 5px;">
										<strong></small>You must agree with the terms and conditions</small></strong>
									</div>
								@endif
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary">
									<i class="fa fa-btn fa-user"></i> Register
								</button>
							</div>
						</div>

						@if (getenv("GITHUB_ID") || getenv("TWITTER_ID") || getenv("FACEBOOK_ID") || getenv("LINKEDIN_ID"))
							<hr>
							<div class="form-group">
								<div class="col-md-6 col-md-offset-4">
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
