<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="description" content="Interactive Curriculum Tutoring Software">
    <meta name="keywords" content="tutor,software,curriculum,school,interactive curriculum,math,reading,home school,homeschool curriculum,phonics,math,star systems,star learning systems,hippo,hippo software,hippo curriculum">
    <meta name="author" content="Star Learning Systems">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>Dashboard | @yield('title', 'Home')</title>

    @include('partial.admin.headers')

	<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<a name="top"></a>
<header class="navbar navbar-gold navbar-fixed-top">
    <nav class="container" role="navigation">
        <div class="navbar-header">
            <a href="{{ route('admin.dashboard') }}" class="navbar-brand">
                <span>Admin Dashboard</span>
            </a>
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        @include('partial.admin.navigation')
    </nav>
</header>
<div id="overlay" class="hidden"></div>
<div class="main_wrapper">
    @if (Session::has('alert'))
        <div class="alert-box">
            <div class="alert alert-{{ Session::get('alert')['status'] }}">
                <div class="container text-center">
                    {{ Session::get('alert')['message'] }}
                </div>
            </div>
        </div>
    @endif
    <div id="primary-content" class="content center-block">
        <div class="page_start container">@yield('content')</div>
    </div>
    <a href="#top" class="text-center hidden-sm hidden-xs">
        <img class="scroll-btn hidden" alt="scroll to top button" title="Back to Top" src="{{ asset('/img/nav/scrollbutton-small.png') }}">
    </a>
</div>
@include('partial.admin.footer')
</body>
</html>
