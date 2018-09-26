@extends('layouts.app')

@section('content')

    <h2 class="ui header">{{ $bar->name }}</h2>

    <p>@lang('pages.bar.joinQuestion')</p>

    {!! Form::open(['action' => ['BarController@doJoin', 'barId' => $bar->id], 'method' => 'POST', 'class' => 'ui form']) !!}
        @if(!$community->isJoined(barauth()->getSessionUser()))
            <div class="inline field">
                <div class="ui toggle checkbox">
                    <input type="checkbox" name="join_community" tabindex="0" class="hidden" checked="checked">
                    {{ Form::label('join_community', __('pages.bar.alsoJoinCommunity') . ': ' . $community->name) }}
                </div>
                {{ ErrorRenderer::inline('join_community') }}
            </div>
        @else
            <p>
                @lang('pages.bar.alreadyJoinedTheirCommunity'):
                {{ $community->name }}
            </p>
        @endif

        <br />

        <div class="ui buttons">
            <button class="ui button positive" type="submit">@lang('pages.bar.yesJoin')</button>
            <div class="or" data-text="@lang('general.or')"></div>
            <a href="{{ route('bar.show', ['barId' => $bar->id]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
        </div>
    {!! Form::close() !!}

@endsection
