@extends('layouts.app')

@section('content')

    <h2 class="ui header">@lang('pages.requestPasswordReset')</h2>

    {!! Form::open(['action' => ['PasswordForgetController@doRequest'], 'method' => 'POST', 'class' => 'ui form']) !!}

        <div class="field {{ ErrorRenderer::hasError('email') ? 'error' : '' }}">
            {{ Form::label('email', __('account.email') . ':') }}
            {{ Form::text('email', '', ['placeholder' => __('account.emailPlaceholder')]) }}
            {{ ErrorRenderer::inline('email') }}
        </div>

        <div>
            <button class="ui button primary" type="submit">@lang('account.resetPassword')</button>
            <a href="{{ route('login') }}" class="ui button basic">@lang('auth.login')</a>
        </div>

    {!! Form::close() !!}

@endsection
