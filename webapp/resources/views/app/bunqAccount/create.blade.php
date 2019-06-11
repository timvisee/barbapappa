@extends('layouts.app')

@section('title', __('pages.bunqAccounts.addAccount'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open(['action' => ['AppBunqAccountController@doCreate'], 'method' => 'POST', 'class' => 'ui form']) !!}
        <div class="field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.descriptiveName') . ' :') }}
            {{ Form::text('name', '', ['placeholder' => __('pages.bunqAccounts.descriptionPlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <div class="ui top attached info message visible">
            <span class="halflings halflings-info-sign"></span>
            @lang('pages.bunqAccounts.tokenDescription')
        </div>
        <div class="ui segment bottom attached">
            <div class="field {{ ErrorRenderer::hasError('token') ? 'error' : '' }}">
                {{ Form::label('token', __('misc.token') . ':') }}
                {{ Form::text('token', '', ['autocomplete' => 'off']) }}
                {{ ErrorRenderer::inline('token') }}
            </div>
        </div>

        <div class="ui top attached info message visible">
            <span class="halflings halflings-info-sign"></span>
            @lang('pages.bunqAccounts.ibanDescription')
        </div>
        <div class="ui segment bottom attached">
            <div class="two fields">
                <div class="field {{ ErrorRenderer::hasError('iban') ? 'error' : '' }}">
                    {{ Form::label('iban', __('barpay::misc.iban') . ':') }}
                    {{ Form::text('iban', '', [
                        'placeholder' => __('barpay::misc.ibanPlaceholder'),
                    ]) }}
                    {{ ErrorRenderer::inline('iban') }}
                </div>

                <div class="field {{ ErrorRenderer::hasError('account_holder') ? 'error' : '' }}">
                    {{ Form::label('account_holder', __('barpay::misc.accountHolder') . ':') }}
                    {{ Form::text('account_holder', '', [
                        'placeholder' => __('account.firstNamePlaceholder') . ' ' .  __('account.lastNamePlaceholder'),
                    ]) }}
                    {{ ErrorRenderer::inline('account_holder') }}
                </div>
            </div>
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('enabled') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('enabled', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('enabled', __('pages.bunqAccounts.enabled')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('enabled') }}
        </div>

        <div class="ui divider hidden"></div>

        <div class="ui warning top attached message visible">
            <span class="halflings halflings-warning-sign"></span>
            @lang('pages.bunqAccounts.addConfirm', ['app' => config('app.name')])
        </div>

        <div class="ui segment bottom attached">
            <div class="inline field {{ ErrorRenderer::hasError('confirm') ? 'error' : '' }}">
                <div class="ui checkbox">
                    {{ Form::checkbox('confirm', true, false, ['tabindex' => 0, 'class' => 'hidden']) }}
                    {{ Form::label('confirm', __('pages.bunqAccounts.confirm')) }}
                </div>
                <br />
                {{ ErrorRenderer::inline('confirm') }}
            </div>
        </div>

        <div class="ui divider hidden"></div>

        <button class="ui button primary" type="submit">@lang('misc.add')</button>
        <a href="{{ route('app.bunqAccount.index') }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>
    {!! Form::close() !!}
@endsection
