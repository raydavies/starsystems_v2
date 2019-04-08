@extends('layout.page')

@section('title', 'The Interactive Curriculum')

@section('headscripts')
	@parent
@stop

@section('page_content')
	<header class="header header-xl text-center">Take a tour of the Interactive Curriculum!</header>

	<div id="curriculum-tour" class="carousel slide margin-top-15" data-interval="false">
		<ol class="carousel-indicators">
			<li data-target="#curriculum-tour" data-slide-to="0" class="active"></li>
			<li data-target="#curriculum-tour" data-slide-to="1"></li>
			<li data-target="#curriculum-tour" data-slide-to="2"></li>
			<li data-target="#curriculum-tour" data-slide-to="3"></li>
			<li data-target="#curriculum-tour" data-slide-to="4"></li>
			<li data-target="#curriculum-tour" data-slide-to="5"></li>
			<li data-target="#curriculum-tour" data-slide-to="6"></li>
		</ol>

		<div class="carousel-inner" role="listbox">
			<div class="item row clearfix active">
				<img src="{{ asset('/img/curriculum/screen_math_sm.jpg') }}" alt="..." class="col-lg-6 col-xs-12 img-responsive">
				<div class="col-lg-6 col-xs-12 caption">
					<header><strong>Choose your lesson</strong></header>
					<p>Select a grade level and general topic to begin.</p>
					<p>Each topic is divided into several in-depth lessons, making it easy to find exactly what you need.</p>
					<p>Clicking on a lesson allows you to choose from four modes: Study, Practice, Essay, and Test. Let's take a look at each.</p>
				</div>
			</div>
			<div class="item row clearfix">
				<img src="{{ asset('/img/curriculum/study_math_sm.jpg') }}" alt="..." class="col-lg-6 col-xs-12 img-responsive">
				<div class="col-lg-6 col-xs-12 caption">
					<header><strong>Study the lesson</strong></header>
					<p>Study mode is the backbone of the program and contains everything students need to know for school and homework.</p>
					<p>There's <u>no</u> filler in the Interactive Curriculum! All lessons are short, engaging, and full of meaningful academic content.</p>
					<p>Students can control the pace with an easy-to-use page turner, and can click any hyperlinks in the text to trigger pop-up boxes filled with additional information.</p>
					<p>Graphics and teacher narration accompany many of the lessons, providing further clarification of the material.</p>
				</div>
			</div>
			<div class="item row clearfix">
				<img src="{{ asset('/img/curriculum/practice_math_sm.jpg') }}" alt="..." class="col-lg-6 col-xs-12 img-responsive">
				<div class="col-lg-6 col-xs-12 caption">
					<header><strong>Practice the lesson</strong></header>
					<p>Practice mode allows students to apply what they've learned in a quiz format. Feedback is immediate; students receive the help they need, when they need it.</p>
					<p>Fresh new questions each session assures learning, not memorization, while a mixture of question styles helps students build test-taking skills.</p>
					<p>Review scores quickly when finished, and try again!</p>
				</div>
			</div>
			<div class="item row clearfix">
				<img src="{{ asset('/img/curriculum/recess_math_sm.jpg') }}" class="col-lg-6 col-xs-12 img-responsive">
				<div class="col-lg-6 col-xs-12 caption">
					<header><strong>Take A Break</strong></header>
					<p>Recess mode keeps students learning with interactive games.</p>
				</div>
			</div>
			<div class="item row clearfix">
				<img src="{{ asset('/img/curriculum/essay_math_sm.jpg') }}" alt="..." class="col-lg-6 col-xs-12 img-responsive">
				<div class="col-lg-6 col-xs-12 caption">
					<header><strong>Get Creative</strong></header>
					<p>Essay mode encourages students to think more deeply about the material by providing open-ended questions as writing topics.</p>
					<p>There are no wrong answers in Essay mode. Students can express themselves while building strong writing skills!</p>
				</div>
			</div>
			<div class="item row clearfix">
				<img src="{{ asset('/img/curriculum/test_math_sm.jpg') }}" alt="..." class="col-lg-6 col-xs-12 img-responsive">
				<div class="col-lg-6 col-xs-12 caption">
					<header><strong>Take the test</strong></header>
					<p>Test mode recreates a real testing atmosphere. Students must answer all questions before feedback is given. Any missed answers can be reviewed once the test is complete.</p>
					<p>Wrong answer? Don't worry! You can take the test again and again. Answers will be switched and new answers added when re-testing.</p>
					<p>Review your scores and other information with the Progress Report!</p>
				</div>
			</div>
			<div class="item row clearfix">
				<iframe height="405" src="https://www.youtube.com/embed/K8mcfFUghf0?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen class="col-lg-6 col-xs-12 video-slide"></iframe>
				<div class="col-lg-6 col-xs-12 caption">
					<header><strong>See It In Action</strong></header>
					<p>Here's an example of study mode in the Primary (K-3rd grade) level of the Interactive Curriculum. This lesson, on the subject of vowels, is part of the Reading course curriculum.</p>
					<p>Hundreds of carefully-crafted lessons like this await your student on each level of the Interactive Curriculum.</p>
					<p>Help your child excel today with the Interactive Curriculum!</p>
					<a type="button" href="{{ route('contact') }}" class="btn btn-info btn-lg">Request More Information</a>
				</div>
			</div>
		</div>

		<a class="left carousel-control" href="#curriculum-tour" role="button" data-slide="prev">
			<span class="glyphicon-chevron-left fa fa-chevron-left" aria-hidden="true"></span>
			<span class="sr-only">Previous</span>
		</a>
		<a class="right carousel-control" href="#curriculum-tour" role="button" data-slide="next">
			<span class="glyphicon-chevron-right fa fa-chevron-right" aria-hidden="true"></span>
			<span class="sr-only">Next</span>
		</a>
	</div>

	<!--
	<div class="font-plus text-center margin-top-15">Don't leave your child's education to chance. Build success with <strong>The Interactive Curriculum!</strong></div>
	-->
@stop
