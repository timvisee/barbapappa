@extends('layouts.app')

@section('title', __('auth.loginEmail'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <div class="ui warning message visible">
        <span class="halflings halflings-warning-sign"></span>
        @lang('auth.openedInOtherBrowser')
    </div>

    <p>@lang('auth.enterLoginCodeOtherBrowser')</p>

    <h3 class="ui block header">
        <code>{{ $code }}</code>
    </h3>

    <p>@lang('general.or')</p>

    {!! Form::open(['action' => ['AuthController@login', 'token' => $token], 'metho' => 'POST', 'class' => 'ui form']) !!}
        {{ Form::hidden('force', 1) }}
        <button class="ui button basic" type="submit">@lang('auth.continueHereInstead')</button>
    {!! Form::close() !!}

    <div class="ui hidden divider"></div>

    <div class="ui link list">
        @if($loginWithPassword ?? false)
            <a href="{{ route('login') }}" class="item">
                @lang('auth.loginPassword')
            </a>
        @endif
        <a href="{{ route('contact') }}" class="item">
            @lang('auth.loginTroubleContact')
        </a>
        <a href="{{ route('index') }}" class="item">
            @lang('pages.index.backToIndex')
        </a>
    </div>
@endsection
