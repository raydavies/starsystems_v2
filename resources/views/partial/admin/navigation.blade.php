<ul class="nav navbar-nav navbar-right collapse navbar-collapse">
    <li>
        <a href="{{ route('home') }}">Site Home</a>
    </li>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">My Account <span class="caret"></span></a>
        <ul class="dropdown-menu" role="menu">
            <li><a href="{{ route('admin.account.info') }}">Account Information</a></li>
            <li><a href="{{ route('admin.password.change') }}">Change Password</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Customer Management <span class="caret"></span></a>
        <ul class="dropdown-menu" role="menu">
            <li><a href="{{ route('admin.customers') }}">Customer Database @if ($new_user_count) <span class="badge progress-bar-info">{{ $new_user_count }}</span> @endif</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Site Management <span class="caret"></span></a>
        <ul class="dropdown-menu" role="menu">
            <li><a href="{{ route('admin.testimonials') }}">Testimonials Manager</a></li>
        </ul>
    </li>
    <p class="navbar-text">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }} <span class="font-sm">(<a href="{{ route('logout') }}">Log Out</a>)</span></p>
</ul>
