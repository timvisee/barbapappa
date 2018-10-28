@extends('layouts.app')

@section('content')

    <h2 class="ui header">{{ $member->name }}</h2>
    <p>@lang('pages.barMembers.deleteQuestion')</p>

    <div class="ui warning message visible">
        <span class="halflings halflings-warning-sign"></span>
        @lang('misc.cannotBeUndone')
    </div>

    <div class="ui divider"></div>

    {{-- {!! Form::open(['action' => ['BarController@doLeave', 'barId' => $bar->human_id], 'method' => 'POST', 'class' => 'ui form']) !!} --}}
    {{--     <div class="ui buttons"> --}}
    {{--         <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}" --}}
    {{--                 class="ui button negative"> --}}
    {{--             @lang('general.noGoBack') --}}
    {{--         </a> --}}
    {{--         <div class="or" data-text="@lang('general.or')"></div> --}}
    {{--         <button class="ui button positive basic" type="submit">@lang('general.yesContinue')</button> --}}
    {{--     </div> --}}
    {{-- {!! Form::close() !!} --}}

@endsection
