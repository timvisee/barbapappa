@extends('layouts.app')

@section('title', __('pages.requestPasswordReset'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open(['action' => ['PasswordForgetController@doRequest'], 'method' => 'POST', 'class' => 'ui form']) !!}

        <div class="required field {{ ErrorRenderer::hasError('email') ? 'error' : '' }}">
            {{ Form::label('email', __('account.email') . ':') }}
            {{ Form::text('email', '', ['type' => 'email', 'placeholder' => __('account.emailPlaceholder')]) }}
            {{ ErrorRenderer::inline('email') }}
        </div>

        <div>
            <button class="ui button primary" type="submit">@lang('account.resetPassword')</button>

            <div class="ui link list">
                <a href="{{ route('login') }}" class="item">@lang('auth.login')</a>
            </div>
        </div>

        @if(is_recaptcha_enabled())
            {!! RecaptchaV3::initJs() !!}
            {!! RecaptchaV3::field('request-password') !!}
        @endif

    {!! Form::close() !!}
@endsection
