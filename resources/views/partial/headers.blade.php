<link rel="icon" href="{{ asset('/img/hippoicon.ico') }}" type="image/x-icon" />
<link rel="shortcut icon" href="{{ asset('/img/hippoicon.ico') }}" type="image/x-icon" />

@section('headstyles')
	<link rel="stylesheet" href="{{ asset('/css/vendor/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('/css/vendor/normalize.min.css') }}">
	<link rel="stylesheet" href="{{ asset('/css/vendor/font-awesome-4.7.0/css/font-awesome.min.css') }}">
	<link rel="stylesheet" href="{{ mix('css/app.css') }}">
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Corben:bold">
@show

@section('headscripts')
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="{{ asset('/js/vendor/bootstrap.min.js') }}" async></script>
	<script src="{{ asset('/js/vendor/jquery.touchSwipe.min.js') }}"></script>
	<script src="{{ asset('/js/legacy.js') }}"></script>
	<script src="{{ mix('js/app.js') }}" async></script>
@show
