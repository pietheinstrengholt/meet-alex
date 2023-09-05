<!-- /resources/views/meettheteam/index.blade.php -->
@extends('layouts.app')

<style>
.team {
	padding:75px 0;
}
h6.description {
	font-weight: bold;
	letter-spacing: 2px;
	color: #999;
	border-bottom: 1px solid rgba(0, 0, 0,0.1);
	padding-bottom: 5px;
}
.profile {
	margin-top: 25px;
}
.profile h1 {
	font-weight: normal;
	font-size: 20px;
	margin:10px 0 0 0;
}
.profile h2 {
	font-size: 14px;
	font-weight: lighter;
	margin-top: 5px;
}
.profile .img-box {
	opacity: 1;
	display: block;
	/* position: relative; */
}
.profile .img-box:after {
	content:"";
	opacity: 0;
	background-color: rgba(102,102,102,0.75);
	position: absolute;
	right: 0;
	left: 0;
	top: 0;
	bottom: 0;
}
.img-box ul {
	position: absolute;
	z-index: 2;
	bottom: 50px;
	text-align: center;
	width: 100%;
	padding-left: 0px;
	height: 0px;
	margin:0px;
	opacity: 0;
}
.profile .img-box:after, .img-box ul, .img-box ul li {
	-webkit-transition: all 0.5s ease-in-out 0s;
	-moz-transition: all 0.5s ease-in-out 0s;
	transition: all 0.5s ease-in-out 0s;
}
.img-box ul i {
	font-size: 20px;
	letter-spacing: 10px;
}
.img-box ul li {
	width: 30px;
	height: 30px;
	text-align: center;
	border: 1px solid #88C425;
	margin: 2px;
	padding: 5px;
	display: inline-block;
}
.img-box a {
	color:#fff;
}
.img-box:hover:after {
	opacity: 1;
}
.img-box:hover ul {
	opacity: 1;
}
.img-box ul a {
	-webkit-transition: all 0.3s ease-in-out 0s;
	-moz-transition: all 0.3s ease-in-out 0s;
	transition: all 0.3s ease-in-out 0s;
}
.img-box a:hover li{
	border-color: #fff;
	color: #88C425;
}
a {
	color:#88C425;
}
a:hover {
	text-decoration:none;
	color:#519548;
}
i.red {
	color:#BC0213;
}

img.profile {
	border-radius: 49.9%;
	border: 2px solid #fff;
	box-shadow: inset 0 1.5px 3px 0 rgba(0,0,0,.15), 0 1.5px 3px 0 rgba(0,0,0,.15);
	width: 72px;
	height: 72px;
}
</style>

@section('content')

	<div class="container">
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
			<div class="col-lg-12">
				<h6 class="description">OUR TEAM</h6>
				<div class="row pt-md">
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 profile">
					<div class="img-box">
					<img class="profile" src="{{ URL::asset('img/meettheteam/piethein.jpg') }}" class="img-responsive">
					<ul class="text-center">
						<a href="https://github.com/pietheinstrengholt"><li><i class="fa fa-github"></i></li></a>
						<a href="https://nl.linkedin.com/in/pietheinstrengholt"><li><i class="fa fa-linkedin"></i></li></a>
					</ul>
					</div>
					<h1>Piethein Strengholt</h1>
					<h2>Architect & Full Stack Developer</h2>
					<p>Piethein works primarily a Data Architect, but strongly believes that programming teaches you how to think. Piethein delivered most of the backend, middleware, API's and a large part of the front-end of meet-Alex. He's into techniques like PHP (Laravel framework), JavaScript, jQuery, HTML, CSS (Bootstrap), but also knows how to look at applications from a broader perspective.</p>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 profile">
					<div class="img-box">
					<img class="profile" src="{{ URL::asset('img/meettheteam/michael.jpg') }}" class="img-responsive">
					<ul class="text-center">
						<a href="https://github.com/Hoogkamer"><li><i class="fa fa-github"></i></li></a>
						<a href="https://nl.linkedin.com/in/michaelhoogkamer"><li><i class="fa fa-linkedin"></i></li></a>
					</ul>
					</div>
					<h1>Michael Hoogkamer</h1>
					<h2>D3 & JS Developer</h2>
					<p>Michael has done an amazing job on the visual aspects of the tooling, such as the spider visualisation. Since he started experimenting with D3js he quickly matured his skills and developed a framework using the d3 force capabilities of D3js combined with jQuery. This framework is now also part of the meet-Alex application.</p>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 profile">
					<div class="img-box">
					<img class="profile" src="{{ URL::asset('img/meettheteam/janmark.jpg') }}" class="img-responsive">
					<ul class="text-center">
						<a href="https://github.com/JMatGitHub"><li><i class="fa fa-github"></i></li></a>
						<a href="https://nl.linkedin.com/in/jan-mark-pleijsant-51289a2"><li><i class="fa fa-linkedin"></i></li></a>
					</ul>
					</div>
					<h1>Jan Mark Pleijsant</h1>
					<h2>Information Modeller</h2>
					<p>Jan Mark is one of the best experts on the Information Modelling subject. He has won an international price with his sharp vision on this subject. His knowledge has greatly influenced the direction of meet-Alex.</p>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 profile">
					<div class="img-box">
					<img class="profile" src="{{ URL::asset('img/meettheteam/roxanne.jpg') }}" class="img-responsive">
					<ul class="text-center">
						<a href="https://github.com/RoxanneNathalie"><li><i class="fa fa-github"></i></li></a>
						<a href="https://nl.linkedin.com/in/roxanne-happé-1042583b"><li><i class="fa fa-linkedin"></i></li></a>
					</ul>
					</div>
					<h1>Roxanne Happé</h1>
					<h2>Visual design and styling</h2>
					<p>Roxanne is very creative and used her talent to do an awesome job on the logo's, visuals and branding of meet-Alex. Beside her talent Roxanne is also very punctual so you better be aware that she won't be chasing you!</p>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 profile">
					<div class="img-box">
					<img class="profile" src="{{ URL::asset('img/meettheteam/santhosh.jpg') }}" class="img-responsive">
					<ul class="text-center">
						<a href="https://github.com/copyrightme"><li><i class="fa fa-github"></i></li></a>
						<a href="https://nl.linkedin.com/in/santhoshpillai"><li><i class="fa fa-linkedin"></i></li></a>
					</ul>
					</div>
					<h1>Santhosh Pillai</h1>
					<h2>Team lead & Data visionary</h2>
					<p>Santhosh is our data visionary and always has a compelling belief in certain things. He's the founding father of the Multi Pyramid, Crowd Sourcing vision where models will be shared, stored and reused.</p>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 profile">
					<div class="img-box">
					<img class="profile" src="{{ URL::asset('img/meettheteam/marijne.jpg') }}" class="img-responsive">
					<ul class="text-center">
						<a href="https://github.com/MarijneLeComte"><li><i class="fa fa-github"></i></li></a>
						<a href="https://nl.linkedin.com/in/marijne-le-comte-46b3737"><li><i class="fa fa-linkedin"></i></li></a>
					</ul>
					</div>
					<h1>Marijne (Lautenbach) le Comte</h1>
					<h2>Audit, Control and Challenger</h2>
					<p>Marijne is more the silent power behind meet-Alex. Given her Audit backround, Marijne takes care of all the important aspects like legal, intellectual, regulatory and compliancy requirements.</p>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 profile">
					<div class="img-box">
					<img class="profile" src="{{ URL::asset('img/meettheteam/anton.jpg') }}" class="img-responsive">
					<ul class="text-center">
						<a href="https://github.com/cruysheer"><li><i class="fa fa-github"></i></li></a>
						<a href="https://nl.linkedin.com/in/contactmetanton"><li><i class="fa fa-linkedin"></i></li></a>
					</ul>
					</div>
					<h1>Anton Cruysheer</h1>
					<h2>Visionary & Challenger</h2>
					<p>Anton is more seen as a challenger. He is used to do the out of the box thinking and looks at meet-Alex from different perspectives. He's also very good at raising new idea's and seeing opportunities.</p>
				</div>
				</div>
			</div>
			</div>
		</div>
	</div>

@endsection
