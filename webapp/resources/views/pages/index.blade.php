@extends('layouts.app')

@section('title', __('misc.welcome'))

@section('content')
    <div class="highlight-box">
        <h2 class="ui header">@lang('misc.welcomeTo')</h2>
        {{ logo()->element(true, ['class' => 'logo']) }}
    </div>

    @if(config('app.auth_session_link'))
        {!! Form::open(['action' => ['AuthController@doContinue'], 'method' => 'POST', 'class' => 'ui form']) !!}

        <p>@lang('pages.index.emailAndContinue')</p>

        <div class="field {{ ErrorRenderer::hasError('email') ? 'error' : '' }}">
            {{ Form::label('email', __('account.email') . ':') }}
            <div class="ui action input">
                {{ Form::text('email', '', ['placeholder' => __('account.emailPlaceholder')]) }}
                <button class="ui button positive" type="submit">@lang('misc.continue')</button>
            </div>
            {{ ErrorRenderer::inline('email') }}
        </div>
        <br>

        {!! Form::close() !!}
    @else
        <div class="ui stackable two column grid">
            <div class="column">
                <a href="{{ route('login') }}" class="ui button fluid large">@lang('auth.login')</a>
            </div>
            <div class="column">
                <a href="{{ route('register') }}" class="ui button fluid large">@lang('auth.register')</a>
            </div>
        </div>
    @endif
@endsection
