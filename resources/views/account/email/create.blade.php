@extends('layouts.app')

@section('content')

    <h1>@lang('pages.accountPage.addEmail.title')</h1>

    {!! Form::open(['action' => ['EmailController@doCreate', 'userId' => $user->id], 'method' => 'POST']) !!}

        <p>@lang('pages.accountPage.addEmail.description')</p>

        {{ Form::label('email', __('account.email') . ':') }}
        {{ Form::text('email', '', ['placeholder' => __('account.emailPlaceholder')]) }}
        {{ ErrorRenderer::inline('email') }}

        {{ Form::submit(__('misc.add')) }}

    {!! Form::close() !!}

@endsection
