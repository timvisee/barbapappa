@extends('layouts.app')

@section('content')

    <h2 class="ui header">{{ $community->name }}</h2>

    <p>@lang('pages.community.leaveQuestion')</p>
    <p>@lang('misc.cannotBeUndone')</p>

    {{-- TODO confirm with toggle --}}

    {!! Form::open(['action' => ['CommunityController@doLeave', 'communityId' => $community->id], 'method' => 'POST', 'class' => 'ui form']) !!}
        <div class="ui buttons">
            <a href="{{ route('community.show', ['communityId' => $community->id]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesContinue')</button>
        </div>
    {!! Form::close() !!}

@endsection
