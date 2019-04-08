@extends('layout.page')

@section('title', 'Lessons Taught')

@section('headscripts')
	@parent
@stop

@section('page_content')
	<div class="row clearfix">
        <div class="lesson_picker_container col-md-8">
            <p class="lesson_text font-plus"><span class="font-bold font-lrg">The Interactive Curriculum</span> has over 2700 Teaching and Study Lessons designed to help students excel. Take a peek at what your child will be learning!</p>
            <img id="blackboard_child" class="img-responsive center-block hidden-xs" src="{{ asset('/img/content/girl-at-blackboard.jpg') }}" alt="Girl at blackboard" title="Girl at blackboard">

            <div class="lesson_picker bg-info">
                <header class="text-info font-plus font-bold">Curious about the curriculum?</header>
                <form method="get" action="{{ route('lessons') }}" id="lesson_select_form" accept-charset="UTF-8">
                    @csrf
                    <div class="form-group">
                        <label for="level_select" class="control-label">Choose a level</label>
                        <select required name="level" id="level_select" class="form-control" aria-describedby="level_select_status">
                            @foreach ($levels as $level)
                                <option value="{{ $level->id }}" @if ($current_level->id === $level->id) selected="selected" @endif>{{ $level->name }} ({{ $level->grade_range }})</option>
                            @endforeach
                        </select>
                        <span id="level_select_status" class="sr-only hidden"></span>
                    </div>

                    <div class="form-group">
                        <label for="subject_select" class="control-label">Choose a subject</label>
                        <select required name="subject" id="subject_select" class="form-control" aria-describedby="subject_select_status">
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @if ($current_subject->id === $subject->id) selected="selected" @endif>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        <span id="subject_select_status" class="sr-only hidden"></span>
                    </div>
                    <button class="btn btn-primary btn-lg" type="submit">View Lessons</button>
                </form>
            </div>
        </div>

        <div class="lesson_catalog text-center col-md-4">
            @if (isset($current_level, $current_subject))
                <h2>{{ $current_level->name }}: {{ $current_subject->name }}</h2>
                @if (count($lessons))
                    <ul class="lesson_list text-left list-group">
                        @foreach ($lessons as $lesson)
                            <li class="lesson-title list-group-item">{{ $lesson->title }}</li>
                        @endforeach
                    </ul>
                @else
                    <div class="empty_list">No lessons found for this subject</div>
                @endif
            @endif
        </div>
	</div>
@stop
