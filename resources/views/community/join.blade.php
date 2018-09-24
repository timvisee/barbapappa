@extends('layouts.app')

@section('content')

    <h2 class="ui header">{{ $community->name }}</h2>

    <p>@lang('pages.community.joinQuestion')</p>

    {!! Form::open(['action' => ['CommunityController@doJoin', 'communityId' => $community->id], 'method' => 'POST', 'class' => 'ui form']) !!}
        <div class="ui buttons">
            <button class="ui button positive" type="submit">@lang('pages.community.yesJoin')</button>
            <div class="or" data-text="@lang('general.or')"></div>
            <a href="{{ route('community.show', ['communityId' => $community->id]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
        </div>
    {!! Form::close() !!}

@endsection
