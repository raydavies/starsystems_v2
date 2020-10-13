@extends('layout.page')

@section('title', 'Create A Testimonial')

@section('headscripts')
    @parent
    <script>
        $(document).ready(function() {
            var form = new FormManager($('#testimonial_form'), {
                'name': 'required|validName',
                'city': 'required|validName',
                'state_province': 'required|validStateAbbr',
                'comment': 'required'
            });
            form.init();
        });
    </script>
@stop

@section('page_content')
    <section id="testimonial_creator" class="row">
        <div class="col-md-12">
            <p class="lead text-center">Submit a testimonial to let us know what you think of the Interactive Curriculum. We love to hear your thoughts!</p>
			<form method="post" action="{{ route('testimonials.create') }}" accept-charset="UTF-8" id="testimonial_form" data-validate-on-start="{{ !empty($errors->all()) }}" novalidate="novalidate">
                @csrf
                <div class="row clearfix">
                    <div class="form-group col-md-6 col-md-offset-3 col-xs-12 has-feedback">
                        <label for="name" class="control-label">Name</label>
                        <div class="control-input">
                            <input required type="text" name="name" id="name" class="form-control" aria-describedby="name_status" value="{{ $testimonial->name }}">
                            <i class="fa form-control-feedback hidden" aria-hidden="true"></i>
                            <span id="name_status" class="sr-only hidden"></span>
                        </div>
                        <div class="control-message">
                            <span class="errormsg">{{ $errors->first('name') }}</span>
                        </div>
                    </div>
                </div>

                <div class="row clearfix">
                    <div class="form-group col-md-4 col-md-offset-3 col-xs-12 has-feedback">
                        <label for="city" class="control-label">City</label>
                        <div class="control-input">
                            <input required type="text" name="city" id="city" class="form-control" aria-describedby="city_status" value="{{ $testimonial->city }}">
                            <i class="fa form-control-feedback hidden" aria-hidden="true"></i>
                            <span id="city_status" class="sr-only hidden"></span>
                        </div>
                        <div class="control-message">
                            <span class="errormsg">{{ $errors->first('city') }}</span>
                        </div>
                    </div>
                    <div class="form-group col-md-2 col-md-offset-0 col-xs-12 has-feedback">
                        <label for="state_province" class="control-label">State</label>
                        <div class="control-input">
                            <select required name="state_province" id="state_province" class="form-control" aria-describedby="state_province_status">
                                @foreach ($states as $state)
                                    <option value="{{ $state->abbreviation }}" @if ($testimonial->state_province === $state->abbreviation) selected="selected" @endif>{{ $state->name }}</option>
                                @endforeach
                            </select>
                            <span id="state_province_status" class="sr-only hidden"></span>
                        </div>
                        <div class="control-message">
                            <span class="errormsg">{{ $errors->first('state_province') }}</span>
                        </div>
                    </div>
                </div>

                <div class="row clearfix">
                    <div class="form-group col-md-6 col-md-offset-3 col-xs-12 has-feedback">
                        <label for="comment" class="control-label">Comments</label>
                        <div class="control-input">
                            <textarea required name="comment" id="comment" class="form-control" aria-describedby="comment_status" cols="50" rows="10">{{ $testimonial->comment }}</textarea>
                            <i class="fa form-control-feedback hidden" aria-hidden="true"></i>
                            <span id="comment_status" class="sr-only hidden"></span>
                        </div>
                        <div class="control-message">
                            <span class="errormsg">{{ $errors->first('comment') }}</span>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success btn-lg">Submit Testimonial</button>
                    <a href="{{ route('testimonials') }}" class="btn btn-danger btn-lg">Cancel</a>
                </div>
            </form>
        </div>
    </section>
@stop
