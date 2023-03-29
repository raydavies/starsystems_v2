@extends('layout.page')

@section('title', 'Frequently Asked Questions')

@section('headscripts')
	@parent
@stop

@section('page_content')
	<header class="header header-xl">Frequently Asked Questions</header>

	<div id="faq" class="text-left margin-top-15">
		<div class="question panel panel-info">
			<div class="panel-heading" data-toggle="collapse" data-target="#answer-0">
				<h3 class="panel-title text-left">What exactly is the Interactive Curriculum Program?</h3>
				<span class="caret caret-large active"></span>
			</div>
			<div id="answer-0" class="answer panel-body panel-collapse in">The Interactive Curriculum is a comprehensive computer program designed especially for home use. The Interactive Curriculum will teach your child the specific topics he or she is expected to learn at each grade level in order to achieve academic success. The program uses interactive teaching and practice lessons on your computer, as well as multimedia and videos lessons along with thousands of printable worksheets which include teaching examples for all subjects. Check out our <a href="https://www.facebook.com/pages/Star-Learning-Systems/1624841284396161?ref=ts&fref=ts" target="_blank">Facebook</a> page for examples of lessons.</div>
		</div>

		<div class="question panel panel-info">
			<div class="panel-heading" data-toggle="collapse" data-target="#answer-1">
				<h3 class="panel-title text-left">What are the computer requirements?</h3>
				<span class="caret caret-large active"></span>
			</div>
			<div id="answer-1" class="answer panel-body panel-collapse in">The Interactive Curriculum will work on any version of Windows, including Windows installations running via Boot Camp on a Mac.</div>
		</div>

		<div class="question panel panel-info">
			<div class="panel-heading" data-toggle="collapse" data-target="#answer-2">
				<h3 class="panel-title text-left">If my child wants to take a practice or test again, will it be the same questions?</h3>
				<span class="caret caret-large active"></span>
			</div>
			<div id="answer-2" class="answer panel-body panel-collapse in">Each time your child clicks on the practice or test button, the software changes the order of some questions and always adds new ones. We call this the 'Teacher Feature'.</div>
		</div>

		<div class="question panel panel-info">
			<div class="panel-heading" data-toggle="collapse" data-target="#answer-3">
				<h3 class="panel-title text-left">My child previously attended a tutoring center. Can he use your software program instead of enrolling again at the tutor?</h3>
				<span class="caret caret-large active"></span>
			</div>
			<div id="answer-3" class="answer panel-body panel-collapse in">Yes, approx. 50% of our customers previously had children going to tutors. However, they now use our program and are amazed at the results. Also, many school teachers see the benefits and are using our software for their own children at home.</div>
		</div>

		<div class="question panel panel-info">
			<div class="panel-heading" data-toggle="collapse" data-target="#answer-4">
				<h3 class="panel-title text-left">How much does the program cost?</h3>
				<span class="caret caret-large active"></span>
			</div>
			<div id="answer-4" class="answer panel-body panel-collapse in">It depends whether you are purchasing a subject, a level, or the entire program (our most popular option). Call (708) 675-7640, or <a href="{{ route('request_info') }}">fill out the form here</a>, for more information.</div>
		</div>

		<div class="question panel panel-info">
			<div class="panel-heading" data-toggle="collapse" data-target="#answer-5">
				<h3 class="panel-title text-left">How do I access the online teaching lessons?</h3>
				<span class="caret caret-large active"></span>
			</div>
			<div id="answer-5" class="answer panel-body panel-collapse in">You can access the online lessons here: <a href="http://www.starlearningsystems.com/wordpress" target="_blank">www.starlearningsystems.com/wordpress</a>. Please note the online lessons are under ongoing development; additional content and features will be available over time. To request a login and password, please call our office at (708) 675-7640.</div>
		</div>
	</div>
@stop
