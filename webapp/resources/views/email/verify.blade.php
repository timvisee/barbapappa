@extends('layouts.app')

@section('content')

    <h2 class="ui header">@lang('pages.verifyEmail.title')</h2>
    <p>@lang('pages.verifyEmail.description')</p>

    {!! Form::open(['action' => ['EmailVerifyController@doVerify'], 'method' => 'POST', 'class' => 'ui form']) !!}

        <div class="field {{ ErrorRenderer::hasError('token') ? 'error' : '' }}">
            {{ Form::label('token', __('misc.token') . ':') }}
            @if(empty($token))
                {{ Form::text('token', '') }}
            @else
                {{ Form::text('token', $token) }}
            @endif
            {{ ErrorRenderer::inline('token') }}
        </div>

        <div>
            <button class="ui button primary" type="submit">@lang('misc.verify')</button>
            <a href="{{ route('dashboard') }}" class="ui button basic">@lang('pages.dashboard')</a>
        </div>

    {!! Form::close() !!}

@endsection
