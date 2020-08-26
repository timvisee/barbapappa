@extends('layouts.app')

@section('title', __('auth.loginEmail'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    @if(isset($email))
        <div class="ui success message">
            <span class="halflings halflings-ok-sign icon"></span>
            @lang('auth.sessionLinkSent', ['email' => $email->email])
        </div>
    @endif

    <p>@lang('misc.emailNotReceivedCheckSpam')</p>

    <div class="ui hidden divider"></div>

    <div class="ui fluid accordion">
        <div class="title">
            <i class="dropdown icon"></i>
            @lang('auth.iHaveVerificationCode')
        </div>
        <div class="content">
            {!! Form::open(['action' => ['AuthController@loginWithCode'], 'method' => 'POST', 'class' => 'ui form']) !!}

            <div class="field {{ ErrorRenderer::hasError('code') ? 'error' : '' }}">
                {{ Form::label('code', __('auth.verificationCode') . ':') }}
                <div class="ui action input">
                    {{ Form::text('code', '', ['placeholder' => __('auth.verificationCodePlaceholder')]) }}
                    <button class="ui button primary" type="submit">@lang('auth.login')</button>
                </div>
                {{ ErrorRenderer::inline('code') }}
            </div>

            {!! Form::close() !!}
        </div>
    </div>

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
