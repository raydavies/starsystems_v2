<ul class="nav navbar-nav navbar-right collapse navbar-collapse">
	@if (Auth::check())
		<li>
			<a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
		</li>
	@endif
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">Our Company <span class="caret"></span></a>
		<ul class="dropdown-menu" role="menu">
			<li><a href="{{ route('about') }}">About Us</a></li>
			<li><a href="{{ route('testimonials') }}">Testimonials</a></li>
		</ul>
	</li>
	<li class="dropdown">
		<a href="#" data-target="#" class="dropdown-toggle" data-toggle="dropdown">Our Product <span class="caret"></span></a>
		<ul class="dropdown-menu" role="menu">
			<li><a href="{{ route('curriculum') }}">The Interactive Curriculum</a></li>
			<li class="divider"></li>
			<li><a href="{{ route('lessons') }}">Lesson Topics</a></li>
			<li><a href="{{ route('benefits') }}">Benefits</a></li>
		</ul>
	</li>
	<li class="dropdown">
		<a href="#" data-target="#" class="dropdown-toggle" data-toggle="dropdown">Support <span class="caret"></span></a>
		<ul class="dropdown-menu" role="menu">
			<li><a href="{{ route('faq') }}">FAQ</a></li>
			<li><a href="{{ route('contact') }}">Contact Us</a></li>
		</ul>
	</li>
</ul>
