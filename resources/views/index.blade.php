<!-- /resources/views/index.blade.php -->
@include('layouts.app')
<head>
	<style>

		b.highlight {
			font-size: larger;
			color: #5F6A76;
		}

		.home {
			overflow-x:hidden;
		}

		/* classes for homepage content, with left and right columns */
		.sectionContainer {
			margin: 0em;
			padding: 0em;
			position:relative;
			z-index:60;
			background-color: #f9f9f9;
		}

		.content {
			margin: 3em 5em 5em 5em; //top right bottom left
		}

		img.IMGcontent {
			width:100%;
			heigh:auto;
		}

	section {
		border-bottom: 1px solid #c7c7c7;
		border-top: 1px solid #c7c7c7;
	}

	.splash {
		padding: 9em 0 2em;
		background-color: #141d27;
		background-image: url(../img/bg.jpg);
		background-size: cover;
		background-attachment: fixed;
		color: #fff;
		text-align: center;
		margin-top: -60px;
	}
	.splash .logo {
		width: 160px
	}
	.splash h1 {
		font-size: 3em
	}
	.splash #social {
		margin: 2em 0
	}
	.splash .alert {
		margin: 2em 0
	}
	@media (max-width:767px) {
		.splash {
			padding-top: 4em
		}
		.splash .logo {
			width: 100px
		}
		.splash h1 {
			font-size: 2em
		}
		#banner {
			margin-bottom: 2em;
			text-align: center
		}
		#splash-header {
			margin-top: 80px;
		}
	}
	.navbar {
		margin-bottom: 0px;
	}
	#myFooter {
		background-color: #2c3e50;
		color: white;
		padding-top: 0px;
		padding-bottom: 0px;
	}
	#myFooter .footer-copyright {
		background-color: #000;
		padding-top: 3px;
		padding-bottom: 3px;
		text-align: center;
	}
	#myFooter .row {
		margin-bottom: 0px;
	}
	#myFooter .navbar-brand {
		margin-top: 45px;
		height: 65px;
	}
	#myFooter .footer-copyright p {
		margin: 10px;
		color: #ccc;
	}
	#myFooter ul {
		list-style-type: none;
		padding-left: 0;
		line-height: 1.7;
	}
	#myFooter h5 {
		font-size: 18px;
		color: white;
		font-weight: bold;
		margin-top: 30px;
	}
	#myFooter h2 a{
		font-size: 50px;
		text-align: center;
		color: #fff;
	}
	#myFooter a {
		color: #d2d1d1;
		text-decoration: none;
	}
	#myFooter a:hover,
	#myFooter a:focus {
		text-decoration: none;
		color: white;
	}
	#myFooter .social-networks {
		text-align: center;
		padding-top: 0px;
		padding-bottom: 0px;
		padding-bottom: 1px;
		margin-top: 25px;
	}
	#myFooter .social-networks a {
		font-size: 32px;
		color: #f9f9f9;
		padding: 10px;
		transition: 0.2s;
	}
	#myFooter .social-networks a:hover {
		text-decoration: none;
	}
	#myFooter .facebook:hover {
		color: #0077e2;
	}
	#myFooter .google:hover {
		color: #ef1a1a;
	}
	#myFooter .twitter:hover {
		color: #00aced;
	}
	#myFooter .btn {
		color: white;
		background-color: #d84b6b;
		border-radius: 20px;
		border: none;
		width: 150px;
		display: block;
		margin: 0 auto;
		margin-top: 10px;
		line-height: 25px;
		margin-bottom: 15px;
	}
	@media screen and (max-width: 767px) {
		#myFooter {
			text-align: center;
		}
	}
	</style>

	<link href="{{ URL::asset('css/parallax.css') }}" rel="stylesheet">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	  <script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	  <script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->

	<script>
	$(function() {
	  if (navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/)) {
		  $('#ios-notice').removeClass('hidden');
		  $('.parallax-container').height($(window).height() * 0.5 | 0);
	  } else {
		$(window).resize(function() {
				var parallaxHeight = Math.max($(window).height() * 0.3, 200) | 0;
				$('.parallax-container').height(parallaxHeight);
			}).trigger('resize');
		}
	});
	</script>

	<script src="{{ URL::asset('js/parallax.1.4.2.modified.js') }}"></script>

</head>

<body class="home">
	<div class="splash">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<h1 id="splash-header">Discover the collections in meet-Alex.<br>And create your own.</h1>
					<!-- my splash animation -->
							<script src="js/splashanimation.js"></script>
							<object id="splash_svg_object" type="image/svg+xml" data="img/splashanimation.svg" width="100%">
								Your browser does not support SVG
							</object>
					<!-- end of my splash animation -->

					<div class="row">
						<div class="col-md-6 col-md-offset-3">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>



	<!-- NEW CONTENT -->



	<!-- SECTION WITH CONTENT -->

	<section class="sectionContainer">

		<div class="content">

			<div class="row">

				<!-- Tomato -->
					<div class="col-xs-12 col-md-6">

						<object type="image/svg+xml" data="img/tomato.svg" id="tomato" style="width:80%;" >
							Your browser does not support SVG
						</object>

					</div>

					<div class="col-xs-12 col-md-6">

							<h2>Not one model fits all</h2>
							<blockquote><p>Ĉu tomato estas frukto aŭ legomo?</p></blockquote>
							<p>Is it a fruit or a vegetable? Opinions may differ. From biological perspective it is a fruit. But from cooking and legal perspective it may be considered a vegetable, as per declaration by the <a href="http://caselaw.findlaw.com/us-supreme-court/149/304.html">US Supreme Court in 1893</a>.
							</p>
							<p>Does it matter? Not really, as long as we have the same understanding about it: a nice, healthy, red thing you can eat and may or may not like. No matter whether we name it a "tomato", or "Solanum Lycopersicum", or even a "love apple".
							</p>

							<blockquote>
								<p>What's in a name? that which we call a rose<br/>By any other name would smell as sweet</p>
								<footer>Shakespeare in <cite title="Source Title">Romeo and Juliet</cite></footer>
							</blockquote>


							<p>The sentence "<strong>Ĉu tomato estas frukto aŭ legomo?</strong>" means "Is a tomato a fruit or a vegetable?" in Esperanto, the artificial language created to become a universal common language bridging cultural and political differences across the world. However, despite its elegance and easy grammar, most people do not speak or understand the language.
							</p>
							<p>Similarly, <strong>there is not one single model or ontology to describe and structure all data</strong> that can efficiently be used and understood by everyone. Different models and languages will always remain.
							</p>


							<h3>What has a tomato to do with data?</h3>
							<p>Suppose you want to know the colour of a tomato. That is data. Or you want to know how many calories a tomato has. That is also data. Maybe described by someone else, in his/her own words, with his/her own interpretation. Nevertheless, you can use that data, as long as you actually mean the same.</p>
							<p>And if you are not interested in tomatoes, you may be interested in data about <strong>customers</strong> (or do you prefer the term "clients"?), <strong>products, loans, transactions, houses, vehicles, medicines, food, planets, stars, equipment, hobbies, etc.</strong>
							</p>

					</div>
			</div>
		</div>
	</section>

		<!-- PARALLAX BACKGROUND -->

		<div class="parallax-container" data-bleed="10" data-image-src="{{ URL::asset('img/background_tomato.jpg') }}" data-natural-height="933" data-natural-width="1400" data-parallax="scroll" data-position="top">
		</div>


		<!-- SECTION WITH CONTENT -->

		<section class="sectionContainer">

			<div class="content">

				<div class="row">
					<div class="col-lg-12">
						<h1>Data is useless without context</h1>
						<h3>An example</h3>
						<p>Suppose someone asks you how many calories a tomato contains. The answer depends on context and interpretation. Questions you should consider, are:
						</p>
					</div>
				</div>

			<div class="row">

					<div class="col-xs-12 col-sm-6">
						<div class="panel panel-default">
  						<div class="panel-heading">
    						<h3 class="panel-title">What type?</h3>
  						</div>
  						<div class="panel-body">
    						What type of tomato is meant? A cherry tomato? A roma tomato? A beefsteak tomato? A salad tomato? The tomato size and amount of calories may differ per type.
  						</div>
						</div>
					</div>

					<div class="col-xs-12 col-sm-6">
						<div class="panel panel-default">
  						<div class="panel-heading">
    						<h3 class="panel-title">Which origin?</h3>
  						</div>
  						<div class="panel-body">
    						Where was the tomato grown? When was the tomato harvested? Age and origin may impact the sugar level in the tomato, and thus the amount of calories.
  						</div>
						</div>
					</div>

					<div class="col-xs-12 col-sm-12">
						<div class="panel panel-default">
  						<div class="panel-heading">
    						<h3 class="panel-title">What is exactly meant?</h3>
  						</div>
  						<div class="panel-body">
								Calorie has two distinct definitions (<em><a href="https://en.oxforddictionaries.com/definition/calorie">Oxford English Dictionary</a></em>):
								<ul class="list-group">
								  <li class="list-group-item">The energy needed to rise the temperature of 1 <strong>gram</strong> of water through 1°C (also known as "small calorie", cal).</li>
								  <li class="list-group-item">The energy needed to rise the temperature of 1 <strong>kilogram</strong> of water through 1°C (also known as "large calorie", Cal).</li>
								</ul>
								Your answer (your data) may be a factor 1000 higher or lower than expected...
  						</div>
						</div>
					</div>

				</div>

				<div class="row">
					<div class="col-lg-12">
						<h2><small>Often this context is not immediately available, leading to different interpretations, hampering efficient data use. We want to change that.</small></h2>
					</div>
				</div>


			</div>

		</section>

		<!-- PARALLAX BACKGROUND -->

		<div class="parallax-container" data-bleed="10" data-image-src="{{ URL::asset('img/background_connected.jpg') }}" data-natural-height="906" data-natural-width="1400" data-parallax="scroll" data-position="top">
		</div>


		<!-- SECTION WITH CONTENT -->

		<section class="sectionContainer">
			<div class="content">

				<div class="row">

					<div class="col-xs-12 col-md-6">

							<h2>Connected collections to describe it all</h2>
							<h2><small>Instead of one overall common model or ontology, we developed a "multi-model approach" to describe and structure data.</small></h2>
							<br/>

								<h3>All data, your data</h3>
								<p>Every second of every day, huge quantities of data are created. Do you know how the data you need is described and interpreted by others? And how your data matches the data need of others?</p>

								<blockquote>
									<p>The size of the digital universe will double every two years at least, a 50-fold growth from 2010 to 2020.</p>
									<footer><a href="https://insidebigdata.com/2017/02/16/the-exponential-growth-of-data/">insideBIGDATA</a> in <cite title="Source Title">The Exponential Growth of Data, February 16, 2017</cite></footer>
								</blockquote>

								<h3>In one place</h3>
								<p>There must be millions of data models to manage all data. Where are these models? Can you connect to these data models to understand and locate the data you need?
								</p>
								<p>Now there is one place where you can store, share, find, and reuse data models: any <strong>collection</strong> of descriptions and structures of data, connected to the interpretation of others.</p>

								<br/>
								<h2><small>These <strong>connected collections</strong> require a solution that is highly scalable.</small></h2>
								<br/>
								<h3>Crowd sourcing</h3>
								<div class="media">
	  							<div class="media-left media-top">
	      						<img class="media-object" src="img/powerofthecrowd.png" alt="power of crowd" style="max-width:100px;">
									</div>
									<div class="media-body">
										<p>Crowd sourcing is key to the solution. The millions of data models can never be collected and maintained by a small group of people. To capture all these descriptions and structures, a very large group of people is needed. Contributing and working together on interconnected collections, to achieve a common understanding.</p>

	  							</div>
									<blockquote>
										<p>The Wisdom of Crowds: Why the Many Are Smarter Than the Few and How Collective Wisdom Shapes Business, Economies, Societies and Nations</p>
										<footer>James Surowiecki</footer>
									</blockquote>
								</div>

								<h3>Ease of use</h3>
								<p>Everyone can use the solution. Just as easy as you draw a model on a whiteboard. And now it is not wiped-out, but stored for later reuse by you and others. Just as easy.</p>

								<h3>Open source</h3>
								<p>We developed the solution as <strong>open source</strong>, and provide it as <strong>open data</strong>. This allows you to contribute to developing new exciting functionality. And it allows you to host the solution in your own environment. Connected to meet-Alex. This is key for scaling up, and for the success of the solution.</p>


					</div>

					<!-- Infographic decribing the purpose of meet-Alex -->
					<div class="col-xs-12 col-md-6">
								<object id="svg-infographicPurpose" type="image/svg+xml" data="img/infographicPurpose.svg" class="embed-responsive-item" width="100%">
									Your browser does not support SVG
								</object>
					</div>
				</div>

			</div>
		</section>

		<!-- PARALLAX BACKGROUND -->

		<div class="parallax-container" data-bleed="10" data-image-src="{{ URL::asset('img/background_describe.jpg') }}" data-natural-height="700" data-natural-width="1400" data-parallax="scroll" data-position="top">
		</div>

		<!-- SECTION WITH CONTENT -->

		<section class="sectionContainer">
			<div class="content">

			<div class="row">

				<!-- Infographic decribing the meta-model of meet-Alex -->
				<div class="col-xs-12 col-md-6">

							<object type="image/svg+xml" data="img/metamodel.svg" class="embed-responsive-item" width="100%">
								Your browser does not support SVG
							</object>

				</div>

				<div class="col-xs-12 col-md-6">

						<h2>Everything you need to describe your data</h2>

							<h3>Term</h3>
							<p>The basic unit in meet-Alex. It's a word or a few words combined, used to name something. For example "tomato", "table", or "dinner table", "customer", "name", "colour", "calorie". All information is basically organised by <strong>terms</strong>.</p>

							<h3>Collection</h3>
							<p>The mechanism to group a number of <strong>terms</strong> by a user. Currently we only allow creating <strong>terms</strong> through a <strong>collection</strong>. But we also allow using <strong>terms</strong> from other collections inside your collection.</p>
							<p><strong>Collections</strong> enable management of <strong>terms</strong> and organise collaboration on <strong>terms</strong>.</p>

							<h3>Relation</h3>
							<p>The mechanism to structure data, by connecting two <strong>terms</strong> inside a <strong>collection</strong>. <strong>Relations</strong> are always stored in the context of a <strong>collection</strong> though some of the terms which are in the <strong>relation</strong> might be used in other <strong>collections</strong>.</p>

							<h3>Description</h3>
							<p>The mechanism to communicate the meaning of a <strong>term</strong> inside a <strong>collection</strong>. Here you can describe what the term represents (for example "tomato is a fruit"), and how it distincts from other terms (for example which characteristic clearly distincts a "tomato" from an "apple"?). </p>

				</div>

			</div>

			</div>
		</section>


		<!-- PARALLAX BACKGROUND -->

		<div class="parallax-container" data-bleed="10" data-image-src="{{ URL::asset('img/background_start.jpg') }}" data-natural-height="933" data-natural-width="1400" data-parallax="scroll" data-position="top">
		</div>

		<!-- SECTION WITH CONTENT -->


	<section class="sectionContainer">

		<div class="content">

		<div class="row">

			<div class="col-xs-12 col-md-6">

					<h2>Get started</h2>

					<div class="list-group">
						<a href="{{ url('/register') }}" class="list-group-item">
							<h4 class="list-group-item-heading">1. Register</h4>
							<p class="list-group-item-text">Create an account or use authorisation via GitHub, linkedIn, Twitter, Facebook.</p>
							<br/><img class="IMGcontent" src="img/screenshot_register.png">
						</a>
            
						<a href="{{ url('/collections') }}" class="list-group-item">
							<h4 class="list-group-item-heading">2. Discover</h4>
							<p class="list-group-item-text">Get an overview of the collections in meet-Alex. And look at the available terms.</p>
							<br/><img class="IMGcontent" src="img/screenshot_discover.png">
						</a>
					</div>

			</div>

			<div class="col-xs-12 col-md-6">

					<h2>Try it out</h2>

					<div class="list-group">
						<a class="list-group-item">
							<h4 class="list-group-item-heading">3. Visualise</h4>
							<p class="list-group-item-text">View a graphical representation of the collection. Try the visual edit mode. Allowing you to draw similarly as drawing on a whiteboard.</p>
							<br/><img class="IMGcontent" src="img/screenshot_visualise.png">
						</a>

						<a href="{{ url('/collections/create') }}" class="list-group-item">
							<h4 class="list-group-item-heading">4. Create</h4>
							<p class="list-group-item-text">Create your own collection and terms.</p>
							<br/><img class="IMGcontent" src="img/screenshot_create.png">
						</a>
					</div>

			</div>

		</div>

		</div>
	</section>



	<!-- PARALLAX BACKGROUND -->

	<div class="parallax-container" data-bleed="10" data-image-src="{{ URL::asset('img/background_road.jpg') }}" data-natural-height="563" data-natural-width="1400" data-parallax="scroll" data-position="top">
	</div>


	<!-- SECTION WITH CONTENT -->

	<section class="sectionContainer">
		<div class="content">

			<div class="row">
				<div class="col-lg-12">
					<h1>Why did we start this journey?</h1>
				</div>
			</div>

			<div class="row">

				<div class="col-xs-12 col-md-4">
						<h3>There is great value in effectively managing data</h3>

						<p>We have a compelling belief that in order to <strong>get in control of data</strong>, you need to be able to <strong>effectively communicate over data</strong>. Making models related to the data enables and facilitates managing data.</p>

						<h3>Scale up the thinking about data models</h3>
						<p>Use of models for data to enable the true potential is known for decades. We learned in a hard way that these decade old techniques are <strong>only known to a very limited number of people</strong>. We started looking for tools in this space and found that they are primarily designed for expert users. This resulted in <strong>lack of scalability</strong>. In the current world, <strong>everybody works with data</strong>, so this is a wasted opportunity to speed up and become more effective within and between organisations on exchanging, analysing and using data.</p>
						<p>None of this definitely stops any organisation to exploit data. <strong>Most organisations and people</strong> already build several systems every day which services many customers. They all <strong>have models implemented</strong> so how is it possible that this subject did not scale?</p>
						<p>We learned that this is due to the fact that people who implement systems uses <strong>modelling techniques which are close to the implementation</strong>. This means, you see the results quickly and adopt accordingly. But this also means that the results<strong> rarely get published</strong> for others in the organisation to reuse and connect to. Often even, <strong>the models get lost again</strong>.</p>
				</div>

				<div class="col-xs-12 col-md-4">
						<h3>Crowd sourcing</h3>
						<p>This led to us with a belief that <strong>"Crowd Sourcing"</strong> is key to achieve success in this area. To enable a place where models can he shared, stored and reused. But to achieve this, we need to keep things simple for people to get started. We also need to be able to accommodate several types of model structures – from simple to complex.</p>
						<h3>Make models understandable by both business and IT</h3>
						<p>Additionally, we have found that <strong>data models</strong> are often used mostly in the IT related part of organisations. They are <strong>rarely discussed with users at other parts of the organisation</strong>. This is one of the key factors in the inefficiency of requirement setting between users of IT and developers of IT. There is almost no discussion on the data which should be in a system. Thus one of our core beliefs is that <strong>we need to enable the dialogue on data between IT people and non-IT people</strong>. Again modelling helps, but only when we can make it simple for non-IT people to read, use and even <strong>build a model on a business level</strong>. This puts demands on our usability.
						</p>
						<h3>Any collection of terms and relations</h3>
						<p>Models at business level require participation of non-IT people. We found that the term "model", and especially "data model" is often recognised by IT people, but less by non-IT people. So we need terminology that makes it more intuitive to non-IT. </p>
						<p>A "data model" is basically a <strong>collection</strong> of <strong>terms</strong> and <strong>relations</strong> between these terms, that are relevant to people in a specific context. The terms are usually explained via a definition or <strong>description</strong>.</p>
						<p>We have found that most people have an intuitive understanding of this explanation, allowing for increased participation. Thus, we refer to "collections" instead of "data models" in meet-Alex.
						</p>
				</div>


				<div class="col-xs-12 col-md-4">
					<h3>Cross the organisation boundary</h3>
					<p>Next to this, we also realise that there are data models ("collections") which are authoritative and common for every individual and organisation (e.g. utility models, legal frameworks, governmental or regulatory frameworks). So the <strong>Crowd Sourcing need to cross the organisation boundary</strong>. So we also came to a conclusion that we should have the code <strong>Open Sourced</strong> and also allow others to host the solution and provide it as <strong>Open Data</strong> and later build a <strong>network of connected data models</strong>. Future plans can also include use of techniques like <strong>Block Chain</strong>.</p>

					<h3>Artificial Intelligence</h3>
					<p>Why cannot Artificial Intelligence (AI) solve this problem? AI can solve part of the problem in this field but not all since it does not replace human intelligence. For supervised learning, we need sufficient data and may be our approach for meet-Alex will provide that data in the future to exploit AI technology to assist in the scalability.
					</p>
				</div>

			</div>

		</div>
	</section>


	<!-- END OF NEW CONTENT -->

	<!-- PARALLAX BACKGROUND -->

	<div class="parallax-container" data-bleed="10" data-image-src="img/background_success.jpg" data-natural-height="511" data-natural-width="1400" data-parallax="scroll" data-position="top"></div>

	<footer id="myFooter">
		<div class="container">
			<div class="row">
				<div class="col-sm-3">
					<img src="{{ URL::asset('img/meet-alex-transparent.png') }}" style="max-width:200px; height:auto; margin-top: 20px;">
				</div>
				<div class="col-sm-2">
					<h5>Get started</h5>
					<ul>
						<li><a href="{{ url('/') }}">Home</a></li>
						<li><a href="{{ url('/register') }}">Sign up</a></li>
					</ul>
				</div>
				<div class="col-sm-2">
					<h5>About us</h5>
					<ul>
						<li><a href="{{ url('/meettheteam') }}">Meet the team</a></li>
						<li><a href="mailto:admin@meet-alex.org">Contact us</a></li>
						<li><a href="#">Help</a></li>
					</ul>
				</div>
				<div class="col-sm-2">
					<h5>Support</h5>
					<ul>
						<li><a href="{{ url('/cookies') }}">Cookie usage</a></li>
					</ul>
				</div>
				<div class="col-sm-3">
					<div class="social-networks">
						<a href="https://www.github.com/meet-Alex/meet-Alex" class="github"><i class="fa fa-github"></i></a>
					</div>
					<button type="button" class="btn btn-default"><a href="mailto:admin@meet-alex.org">Contact us</a></button>
				</div>
			</div>
		</div>
		<div class="footer-copyright">
			<p>meet-Alex for open source and open data - enabling crowd sourcing of information models © 2017 Copyright </p>
		</div>
	</footer>
</body>
