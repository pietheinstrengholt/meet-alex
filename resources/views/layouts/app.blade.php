<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>{!! Settings::get('main_message1') !!}</title>

	<!-- Styles -->
	<link rel="stylesheet" href="{{ URL::asset('css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ URL::asset('css/font-awesome.min.css') }}">
	<link rel="stylesheet" href="{{ URL::asset('css/app.css') }}">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta charset="utf-8">

	<!-- Meta base url, needed for javascript location -->
	<meta name="base_url" content="{{ URL::to('/') }}">

	<!-- Meta csrf token -->
	<meta name="_token" content="{{ csrf_token() }}">

	<!-- IE Console log fix -->
	<script type="text/javascript"> if (!window.console) console = {log: function() {}}; </script>

	<!-- JavaScripts -->
	<script src="{{ URL::asset('js/jquery-3.1.0.min.js') }}"></script>
	<script src="{{ URL::asset('js/bootstrap.min.js') }}"></script>
	<script src="{{ URL::asset('js/jquery.barrating.min.js') }}"></script>
	<script src="{{ URL::asset('js/bootstrap-cookie-consent.js') }}"></script>
	<script src="{{ URL::asset('js/handlebars.js') }}"></script>
	<script src="{{ URL::asset('js/typeahead.bundle.js') }}"></script>
	<script src="{{ URL::asset('js/app.js') }}"></script>
	{{-- If the user is authenticated enable script below to submit ratings --}}
	@if (Auth::check())
		<script src="{{ URL::asset('js/app-submit-ratings.js') }}"></script>
	@else
		{{-- enable the star ratings, but read only --}}
		<script src="{{ URL::asset('js/app-initialize-ratings.js') }}"></script>
	@endif

	<style>
		.fa-btn {
			margin-right: 6px;
		}
		div.container.inner {
			margin-top: 85px;
		}
		.navbar-brand {
			padding: 13.5px 15px 12.5px;
		}
		.navbar-brand>img {
			display: inline;
			margin: 0 10px;
			height: 100%
		}
		.navbar-brand-fake {
			margin-top: 5px;
		}
	</style>

	<!-- Favicon -->
	<link rel="apple-touch-icon" sizes="57x57" href="{{ URL::asset('img/favicon/apple-icon-57x57.png') }}">
	<link rel="apple-touch-icon" sizes="60x60" href="{{ URL::asset('img/favicon/apple-icon-60x60.png') }}">
	<link rel="apple-touch-icon" sizes="72x72" href="{{ URL::asset('img/favicon/apple-icon-72x72.png') }}">
	<link rel="apple-touch-icon" sizes="76x76" href="{{ URL::asset('img/favicon/apple-icon-76x76.png') }}">
	<link rel="apple-touch-icon" sizes="114x114" href="{{ URL::asset('img/favicon/apple-icon-114x114.png') }}">
	<link rel="apple-touch-icon" sizes="120x120" href="{{ URL::asset('img/favicon/apple-icon-120x120.png') }}">
	<link rel="apple-touch-icon" sizes="144x144" href="{{ URL::asset('img/favicon/apple-icon-144x144.png') }}">
	<link rel="apple-touch-icon" sizes="152x152" href="{{ URL::asset('img/favicon/apple-icon-152x152.png') }}">
	<link rel="apple-touch-icon" sizes="180x180" href="{{ URL::asset('img/favicon/apple-icon-180x180.png') }}">
	<link rel="icon" type="image/png" sizes="192x192"	href="{{ URL::asset('img/favicon/android-icon-192x192.png') }}">
	<link rel="icon" type="image/png" sizes="32x32" href="{{ URL::asset('img/favicon/favicon-32x32.png') }}">
	<link rel="icon" type="image/png" sizes="96x96" href="{{ URL::asset('img/favicon/favicon-96x96.png') }}">
	<link rel="icon" type="image/png" sizes="16x16" href="{{ URL::asset('img/favicon/favicon-16x16.png') }}">
	<link rel="manifest" href="{{ URL::asset('img/favicon/manifest.json') }}">
	<meta name="msapplication-TileColor" content="#ffffff') }}">
	<meta name="msapplication-TileImage" content="{{ URL::asset('img/ms-icon-144x144.png') }}">
	<meta name="theme-color" content="#ffffff">
</head>
<body id="app-layout">
	<div class="navbar navbar-default navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<!-- Branding Image -->
				<a onclick="trackClick(this)" href="{{ url('/') }}" class="navbar-brand"><img src="{{ URL::asset('img/navbar-icon.png') }}">meet-Alex</a>
				<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>
			<div class="navbar-collapse collapse" id="navbar-main">
				<ul class="nav navbar-nav">
					<li><a onclick="trackClick(this)" href="{{ route('collections.index') }}">Collections</a></li>
					<li><a onclick="trackClick(this)" href="{{ url('/terms') }}">Terms</a></li>

					<!-- Search bar -->
					{!! Form::open(array('action' => 'SearchController@search', 'class' => 'navbar-form navbar-left')) !!}
					<input type="hidden" name="_token" value="{!! csrf_token() !!}">
					<input type="hidden" name="advanced-search" value="no">
						<div class="form-group">
							<input type="text" name="search" class="form-control typeahead" placeholder="Search for content">
						</div>
						<button type="submit" class="btn btn-default">Submit</button>
					{!! Form::close() !!}
				</ul>

				<!-- Right Side Of Navbar -->
				<ul class="nav navbar-nav navbar-right">
					<!-- Authentication Links -->
					@if (Auth::guest())
						<li><a href="{{ url('/register') }}">Register</a></li>
						<li><a href="{{ url('/login') }}">Login</a></li>
					@else
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
								{{ Auth::user()->name }} <span class="caret"></span>
							</a>

							<ul class="dropdown-menu" role="menu">
								<li><a href="{{ URL::to('/users/bookmarks') }}"><span class="glyphicon glyphicon-grain" aria-hidden="true"></span> Change following models</a></li>
								@if (is_null(Auth::user()->provider))
									<li><a href="{{ URL::to('/users/' . Auth::user()->id . '/edit') }}"><span class="glyphicon glyphicon glyphicon-wrench" aria-hidden="true"></span> Update user details</a></li>
									<li><a href="{{ URL::to('/users/' . Auth::user()->id . '/password') }}"><span class="glyphicon glyphicon glyphicon-wrench" aria-hidden="true"></span> Change password</a></li>
								@endif
								@if (Auth::user()->role == "admin")
									<li class="divider"></li>
									<li><a href="{{ URL::to('/settings') }}"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Settings</a></li>
									<li class="divider"></li>
									<li><a href="{{ URL::to('/groups') }}"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit groups</a></li>
									<li><a href="{{ URL::to('/users') }}"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit users</a></li>
									<li><a href="{{ URL::to('/statuses') }}"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit statuses</a></li>
									<li><a href="{{ URL::to('/defaultrelations') }}"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit default relation types</a></li>
								@endif
								<li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
							</ul>
						</li>
					@endif
				</ul>
			</div>
		</div>
	</div>

	<div class="container inner">
		<!-- Session content -->
		@if (Session::has('message'))
			<div id="session-alert" class="alert alert-info alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<p>{!! Session::get('message') !!}</p>
			</div>
		@endif

		<!-- Content -->
		@yield('content')
	</div>

</body>
</html>
