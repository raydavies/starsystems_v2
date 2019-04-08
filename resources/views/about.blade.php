@extends('layout.page')

@section('title', 'About Us')

@section('headscripts')
	@parent
@stop

@section('page_content')
	<section id="company_info" class="info-section">
		<header class="header header-lrg">About Us</header>
		<div class="container">
			<p class="info-text">Star Learning Systems has been committed to bringing parents and students the highest caliber of learning products for over three decades. Even before computers were commonplace in the classroom or at home, Star was at the forefront of technology in education. While our business has changed over the years, one thing has stayed the same: our dedication to your child's success in school.</p>
		</div>
	</section>
	<section id="product_info" class="info-section">
		<header class="header header-lrg">About the Interactive Curriculum</header>

		<div class="container row">
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#coverage" aria-controls="coverage" role="tab" data-toggle="tab">Comprehensive Coverage</a></li>
				<li role="presentation"><a href="#interaction" aria-controls="interaction" role="tab" data-toggle="tab">One-On-One Interaction</a></li>
				<li role="presentation"><a href="#success" aria-controls="success" role="tab" data-toggle="tab">Success At Home</a></li>
			</ul>

			<div class="tab-content clearfix">
				<article role="tabpanel" id="coverage" class="tab-pane fade in active">
					<img class="img-responsive col-md-6" src="{{ asset('/img/content/students-writing-essay-small.jpg') }}" alt="Children looking at globe">
					<div class="col-md-6">
						<header class="info-header">Comprehensive Coverage</header>
						<p class="info-text">The Interactive Curriculum Software Program covers everything your child needs to know for success in school: <strong><em>Reading, Vocabulary, Grammar and Writing, Math, Science, and Social Studies</em></strong>. Lessons, examples, and exercises build a solid foundation for students and allow them to develop at an accelerated pace.</p>
					</div>
				</article>
				<article role="tabpanel" id="interaction" class="tab-pane fade">
					<img class="img-responsive col-md-6" src="{{ asset('/img/content/teacher-in-front-of-chalkboard-small.jpg') }}" alt="Children looking at globe">
					<div class="col-md-6">
						<header class="info-header">One-On-One Interaction</header>
						<p class="info-text">Owning the Interactive Curriculum Program is like having a full-time teacher working with your child! The program uses a combination of graphics, audio, and animation to <strong>make schoolwork fun and easy</strong>. The interactive lessons engage and guide your child while dynamic quizzes and a variety of writing exercises reinforce what she is learning.</p>
					</div>
				</article>
				<article role="tabpanel" id="success" class="tab-pane fade">
					<img class="img-responsive col-md-6" src="{{ asset('/img/content/father-helping-with-homework-small.jpg') }}" alt="Children looking at globe">
					<div class="col-md-6">
						<header class="info-header">Success At Home</header>
						<p class="info-text">Studies show that children succeed in school when learning takes place at home. <strong>The Home makes a difference</strong>. The Interactive Curriculum was designed by educators to teach and review everything students are learning in the classroom. Your child will find the tools he needs to master the school curriculum, improve test scores, develop strong study skills, and help with homework!</p>
					</div>
				</article>
			</div>
		</div>
	</section>
	<!--<div id="timeline_wrapper">
		<div id="timeline">
			<div id="inner_timeline">
				<div class="history hidden"><h3>1980s</h3>
				Star Learning Systems formed in 1982 as a distributor of early childhood educational products, with a focus on reading comprehension and grammar. The Phonics Reading Program and volumes such as the Encyclopedia Americana and the Grolier New Book of Knowledge formed the core of Star's educational curriculum.
				</div>
				<div class="history hidden"><h3>1990s</h3>
				As computers became increasingly present both at home and in schools, Star moved into the burgeoning field of technology to better provide their educational programs for a modern classroom. Distributing a variety of educational software and computer systems, Star helped a new generation of tech-savvy students learn in a fun and innovative way.
				</div>
				<div class="history hidden"><h3>2000s & beyond</h3>
				With an endless amount of information available online, having exactly what you need in one quick, easy place becomes extremely important. This is what Star believed in 2000 when we became the exclusive distributor of the Interactive Curriculum, a program that pulls together everything students K-12 are expected to know for success in school. Star continues to look to the future, and we want to help children everywhere do the same.
				</div>
			</div>
		</div>
	</div>-->
@stop
