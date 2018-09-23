@extends('layouts.app')

@section('content')

    <h3 class="ui header">@lang('pages.accountPage.addEmail.title')</h3>
    <p>@lang('pages.accountPage.addEmail.description')</p>

    {!! Form::open(['action' => ['EmailController@doCreate', 'userId' => $user->id], 'method' => 'POST', 'class' => 'ui form']) !!}

        <div class="field {{ ErrorRenderer::hasError('email') ? 'error' : '' }}">
            {{ Form::label('email', __('account.email') . ':') }}
            {{ Form::text('email', '', ['placeholder' => __('account.emailPlaceholder')]) }}
            {{ ErrorRenderer::inline('email') }}
        </div>

        <div>
            <button class="ui button primary" type="submit">@lang('misc.add')</button>
        </div>

    {!! Form::close() !!}

@endsection
