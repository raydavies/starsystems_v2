@extends('layout.page')

@section('title', 'Customer Testimonials')

@section('page_content')
    <section id="testimonial_header" class="row">
        <h1 class="col-md-6 col-xs-12"><strong class="header header-xl">Customer Testimonials</strong><br><small>See what people are saying about us!</small></h1>
        <div class="col-md-6 col-xs-12 text-right">
            <a href="{{ route('testimonials.create') }}" role="button" class="btn btn-primary btn-lg testimonial_create_button">Create a testimonial</a>
        </div>
    </section>

    <section id="testimonial_list" class="text-left">
        @foreach ($testimonials as $index => $testimonial)
            @if ($index % 2 == 0)
                <div class="row clearfix">
            @endif
            <article class="col-md-6">
                <div class="testimonial panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title text-left">{{ $testimonial->name ?? 'Anonymous' }} in @if ($testimonial->city){{ $testimonial->city }}, @endif{{ $testimonial->state_province }}</h3>
                    </div>
                    <div id="testimonial-{{ $testimonial->id }}" class="comment panel-body">{{ $testimonial->comment }}</div>
                </div>
            </article>
            @if (($index % 2 !== 0) || ($index == count($testimonials) + 1))
                </div>
            @endif
        @endforeach
    </section>
@stop
