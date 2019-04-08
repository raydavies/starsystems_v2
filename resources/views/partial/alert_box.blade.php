@if (count($errors))
    <div class="alert-box">
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div class="errormsg">{{ $error }}</div>
            @endforeach
        </div>
    </div>
@elseif (Session::get('status'))
    <div class="alert-box">
        <div class="alert alert-info">
            <div class="msg">{{ Session::get('status') }}</div>
        </div>
    </div>
@endif
