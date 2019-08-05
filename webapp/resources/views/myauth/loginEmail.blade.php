@extends('layouts.app')

@section('title', __('auth.login'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open(['action' => ['LoginController@doEmail'], 'method' => 'POST', 'class' => 'ui form']) !!}

    <div class="field {{ ErrorRenderer::hasError('email') ? 'error' : '' }}">
        {{ Form::label('email', __('account.email') . ':') }}
        {{ Form::text('email', '', ['placeholder' => __('account.emailPlaceholder')]) }}
        {{ ErrorRenderer::inline('email') }}
    </div>

    <div>
        <button class="ui button primary" type="submit">@lang('auth.login')</button>
        <a href="{{ route('login') }}" class="ui button basic">@lang('auth.loginPassword')</a>
        <a href="{{ route('register') }}" class="ui button basic">@lang('auth.register')</a>
    </div>

    {!! Form::close() !!}
@endsection
