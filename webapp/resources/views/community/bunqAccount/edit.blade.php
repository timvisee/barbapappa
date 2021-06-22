@extends('layouts.app')

@section('title', $account->name)
@php
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open(['action' => ['BunqAccountController@doEdit', $community->human_id, $account->id], 'method' => 'PUT', 'class' => 'ui form']) !!}
        <div class="required field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.descriptiveName') . ' :') }}
            {{ Form::text('name', $account->name, ['placeholder' => __('pages.bunqAccounts.descriptionPlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <div class="required field {{ ErrorRenderer::hasError('account_holder') ? 'error' : '' }}">
            {{ Form::label('account_holder', __('barpay::misc.accountHolder') . ':') }}
            {{ Form::text('account_holder', $account->account_holder, [
                'placeholder' => __('account.firstNamePlaceholder') . ' ' .  __('account.lastNamePlaceholder'),
            ]) }}
            {{ ErrorRenderer::inline('account_holder') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('enable_payments') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('enable_payments', true, $account->enable_payments, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('enable_payments', __('pages.bunqAccounts.enablePayments')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('enable_payments') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('enable_checks') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('enable_checks', true, $account->enable_checks, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('enable_checks', __('pages.bunqAccounts.enableChecks') . ' (' . __('general.recommended') . ')') }}
            </div>
            <br />
            {{ ErrorRenderer::inline('enable_checks') }}
        </div>

        <div class="ui divider hidden"></div>

        <button class="ui button primary" type="submit">@lang('misc.saveChanges')</button>
        <a href="{{ route('community.bunqAccount.show', ['communityId' => $community->human_id, 'accountId' => $account->id]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>
    {!! Form::close() !!}
@endsection
