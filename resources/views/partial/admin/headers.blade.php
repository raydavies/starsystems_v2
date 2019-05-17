<link rel="icon" href="{{ asset('/img/hippoicon.ico') }}" type="image/x-icon" />
<link rel="shortcut icon" href="{{ asset('/img/hippoicon.ico') }}" type="image/x-icon" />

@section('headstyles')
    <link rel="stylesheet" href="{{ asset('/css/vendor/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/vendor/normalize.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/vendor/fontawesome-5.8.2/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/admin.css') }}">
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Corben:bold">
@show

@section('headscripts')
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="{{ asset('/js/vendor/bootstrap.min.js') }}" async></script>
    <script src="{{ asset('/js/ajax-setup.js') }}" async></script>
    <script src="{{ asset('/js/admin.js') }}" async></script>
@show
