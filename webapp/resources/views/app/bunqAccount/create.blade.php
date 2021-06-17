@extends('layouts.app')

@section('title', __('pages.bunqAccounts.addAccount'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>
        @lang('pages.bunqAccounts.addAccountDescription')
        <a href="{{ route('app.bunqAccount.createSandbox') }}">@lang('pages.bunqAccounts.createSandboxAccount')</a>.
    </p>

    <div class="ui divider hidden"></div>

    {!! Form::open(['action' => ['AppBunqAccountController@doCreate'], 'method' => 'POST', 'class' => 'ui form']) !!}
        <div class="required field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.descriptiveName') . ' :') }}
            {{ Form::text('name', '', ['placeholder' => __('pages.bunqAccounts.descriptionPlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <div class="ui top attached info message visible">
            <span class="halflings halflings-info-sign"></span>
            @lang('pages.bunqAccounts.tokenDescription')
        </div>
        <div class="ui segment bottom attached">
            <div class="two fields">
                <div class="four wide field">
                    <div class="field {{ ErrorRenderer::hasError('language') ? 'error' : '' }}">
                        {{ Form::label('environment', __('pages.bunqAccounts.environment') . ':') }}
                        <div class="ui fluid selection dropdown">
                            {{ Form::hidden('environment', 'production') }}
                            <i class="dropdown icon"></i>

                            <div class="default text">@lang('misc.pleaseSpecify')</div>
                            <div class="menu">
                                <div class="item" data-value="production">
                                    Production
                                </div>
                                <div class="item" data-value="sandbox">
                                    Sandbox
                                </div>
                            </div>
                        </div>
                        {{ ErrorRenderer::inline('environment') }}
                    </div>
                </div>

                <div class="twelve wide field">
                    <div class="required field {{ ErrorRenderer::hasError('token') ? 'error' : '' }}">
                        {{ Form::label('token', __('misc.token') . ':') }}
                        {{ Form::text('token', '', ['autocomplete' => 'off']) }}
                        {{ ErrorRenderer::inline('token') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="ui top attached info message visible">
            <span class="halflings halflings-info-sign"></span>
            @lang('pages.bunqAccounts.ibanDescription')
        </div>
        <div class="ui segment bottom attached">
            <div class="two fields">
                <div class="required field {{ ErrorRenderer::hasError('iban') ? 'error' : '' }}">
                    {{ Form::label('iban', __('barpay::misc.iban') . ':') }}
                    {{ Form::text('iban', '', [
                        'placeholder' => __('barpay::misc.ibanPlaceholder'),
                    ]) }}
                    {{ ErrorRenderer::inline('iban') }}
                </div>

                <div class="required field {{ ErrorRenderer::hasError('account_holder') ? 'error' : '' }}">
                    {{ Form::label('account_holder', __('barpay::misc.accountHolder') . ':') }}
                    {{ Form::text('account_holder', '', [
                        'placeholder' => __('account.firstNamePlaceholder') . ' ' .  __('account.lastNamePlaceholder'),
                    ]) }}
                    {{ ErrorRenderer::inline('account_holder') }}
                </div>
            </div>
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
            @lang('pages.bunqAccounts.addConfirm', ['app' => config('app.name')])
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

        <button class="ui button primary" type="submit">@lang('misc.add')</button>
        <a href="{{ route('app.bunqAccount.index') }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>
    {!! Form::close() !!}
@endsection
