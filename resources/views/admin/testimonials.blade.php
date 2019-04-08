@extends('layout.dashboard')

@section('title', 'Testimonials Manager')

@section('content')
    <header class="header header-xl">Testimonials Manager</header>
    <div class="alert alert-info" role="alert">Use this widget to activate/deactivate testimonials submitted on the site. Inactive testimonials will not be displayed on the site. Coming soon: sort functionality!</div>

    <div id="testimonial_manager">
        <ul id="testimonial_filters" class="nav nav-pills" role="tablist">
            <li role="presentation" class="active"><a href="#" role="tab" data-toggle="tab" data-status="">All</a></li>
            <li role="presentation"><a href="#" role="tab" data-toggle="tab" data-status="1">Active</a></li>
            <li role="presentation"><a href="#" role="tab" data-toggle="tab" data-status="0">Inactive</a></li>
        </ul>

        <section id="testimonial_list" class="outer-wrapper text-left">
            <div class="inner-wrapper row clearfix">
                @foreach ($testimonials as $index => $testimonial)
                    <article class="testimonial-wrapper col-md-6">
                        <div id="testimonial-{{ $testimonial->id }}" class="testimonial panel @if ($testimonial->flag_active) panel-success @else panel-danger @endif">
                            <div class="panel-heading clearfix">
                                <span class="panel-title text-left pull-left font-default"><span class="font-bold">{{ $testimonial->name ?? 'Anonymous' }}</span> | @if ($testimonial->city){{ $testimonial->city }}, @endif{{ $testimonial->state_province }} | {!! $testimonial->created_at->format('M j, Y g:ia') !!}</span>
                                <button type="button" class="close delete-button" aria-label="Delete" data-toggle="tooltip" title="Delete Testimonial">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <label class="activation-toggle-switch pull-right" data-toggle="tooltip" title="Toggle Testimonial Status">
                                    <input name="flag_active" type="checkbox" @if ($testimonial->flag_active) checked @endif>
                                    <div class="slider round"></div>
                                </label>
                            </div>
                            <div class="comment panel-body">{{ $testimonial->comment }}</div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
@stop
