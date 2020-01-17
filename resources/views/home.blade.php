@extends('layout.master')

@section('headscripts')
	@parent
@stop

@section('content')
	<section class="billboard">
		<div class="container container-sm-full">
			<div class="row">
				<div class="blackboard col-md-7 text-center">
					<h1 class="tagline">The Most Comprehensive Educational System Available <small>See how The Interactive Curriculum can help your child</small></h1>
					<ul class="list-inline">
						<li><i class="fa fa-fw fa-check"></i> Excel In School</li>
						<li><i class="fa fa-fw fa-check"></i> Study More Effectively</li>
						<li><i class="fa fa-fw fa-check"></i> Prepare For The Future</li>
					</ul>
					<a class="button-link" href="{{ route('curriculum') }}">
						<button class="btn btn-lg btn-default">Take The Tour</button>
					</a>
				</div>
			</div>
		</div>
	</section>
	<section class="container features">
		<header class="text-center">A Four Step Approach to Learning</header>
		<div class="row clearfix">
			<article class="feature col-md-3">
				<i class="fa fa-fw fa-lg fa-book"></i><span>Study</span> Interactive lessons are your child's first step toward mastering the curriculum and improving in school. Use the study mode for homework help any time, day or night.
			</article>
			<article class="feature col-md-3">
				<i class="far fa-fw fa-lg fa-lightbulb"></i><span>Practice</span> Just like with a live teacher, your child is given instant feedback. Get hands-on with the same material needed for school and standardized tests.
			</article>
			<article class="feature col-md-3">
				<i class="fa fa-fw fa-lg fa-graduation-cap"></i><span>Test</span> Prepare your child <strong><em>before</em></strong> she takes the test in school! Grades are written to the Progress Report for you to review.
			</article>
			<article class="feature col-md-3">
				<i class="fas fa-fw fa-lg fa-pencil-alt"></i><span>Essay</span> Help develop writing ability and research skills while your student exercises his creativity.
			</article>
			<div class="feature cta-buttons col-md-12 text-center">
				<a class="button-link" href="{{ route('curriculum') }}">
					<button class="btn btn-info btn-lg" type="button">Learn More</button>
				</a>
				<a class="button-link" href="{{ route('lessons') }}">
					<button class="btn btn-primary btn-lg" type="button">View Lesson Topics</button>
				</a>
			</div>
		</div>
	</section>
	<section class="slideshow">
		<div class="container container-sm-full">
			<div class="row">
				<div id="slideshow" class="col-md-6 col-md-offset-6 col-xs-12 col-xs-offset-0">
					<div class="levels">
						<p>Available in 4 levels:</p>
						<p class="levels-grades">
							K - 3<sup>rd</sup> grade<br>
							4<sup>th</sup> - 6<sup>th</sup> grade<br>
							7<sup>th</sup> - 9<sup>th</sup> grade<br>
							10<sup>th</sup> - 12<sup>th</sup> grade<br>
						</p>
						<p class="font-lrg">Each level contains hundreds of teaching tutorials and thousands of test questions!</p>
					</div>
					{{--
					<iframe width="853" height="480" src="https://www.youtube.com/embed/videoseries?list=PL09vPPCUB2vCqsPuGIBbHwgw8p7OO_mKA" frameborder="0" allowfullscreen></iframe>
					--}}
				</div>
			</div>
		</div>
	</section>
@stop
