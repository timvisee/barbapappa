@extends('layouts.app')

@section('title', __('pages.bunqAccounts.createSandboxAccount'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open(['action' => ['AppBunqAccountController@doCreateSandbox'], 'method' => 'POST', 'class' => 'ui form']) !!}
        <div class="required field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.descriptiveName') . ' :') }}
            {{ Form::text('name', '', ['placeholder' => __('pages.bunqAccounts.descriptionPlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('enable_payments') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('enable_payments', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('enable_payments', __('pages.bunqAccounts.enablePayments')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('enable_payments') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('enable_checks') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('enable_checks', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('enable_checks', __('pages.bunqAccounts.enableChecks')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('enable_checks') }}
        </div>

        <div class="ui divider hidden"></div>

        <div class="ui warning top attached message visible">
            <span class="halflings halflings-warning-sign"></span>
            @lang('pages.bunqAccounts.createSandboxConfirm', ['app' => config('app.name')])
        </div>

        <div class="ui segment bottom attached">
            <div class="inline required field {{ ErrorRenderer::hasError('confirm') ? 'error' : '' }}">
                <div class="ui checkbox">
                    {{ Form::checkbox('confirm', true, false, ['tabindex' => 0, 'class' => 'hidden']) }}
                    {{ Form::label('confirm', __('pages.bunqAccounts.confirm')) }}
                </div>
                <br />
                {{ ErrorRenderer::inline('confirm') }}
            </div>
        </div>

        <div class="ui divider hidden"></div>

        <button class="ui button primary" type="submit">@lang('misc.create')</button>
        <a href="{{ route('app.bunqAccount.create') }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>
    {!! Form::close() !!}
@endsection
