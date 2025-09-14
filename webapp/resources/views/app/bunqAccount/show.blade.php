@extends('layouts.app')

@section('title', $account->name)
@php
    $breadcrumbs = Breadcrumbs::generate('app.bunqaccount.show', $account);
    $menusection = 'app_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    @if(!is_url_secure())
        <div class="ui warning message">
            <span class="halflings halflings-warning-sign icon"></span>
            @lang('pages.bunqAccounts.noHttpsNoCallbacks')
        </div>
    @endif

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.name')</td>
                <td>{{ $account->name }}</td>
            </tr>
            <tr>
                <td>@lang('pages.bunqAccounts.paymentsEnabled')</td>
                <td>{{ yesno($account->enable_payments) }}</td>
            </tr>
            <tr>
                <td>@lang('pages.bunqAccounts.checksEnabled')</td>
                @if($account->enable_checks)
                    <td>{{ yesno($account->enable_checks) }}</td>
                @else
                    <td><span class="ui text negative">{{ yesno($account->enable_checks) }}</span></td>
                @endif
            </tr>
            <tr>
                <td>@lang('barpay::misc.accountHolder')</td>
                <td>{{ $account->account_holder }}</td>
            </tr>
            <tr>
                <td>@lang('barpay::misc.iban')</td>
                <td>{{ format_iban($account->iban) }}</td>
            </tr>
            <tr>
                <td>@lang('barpay::misc.bic')</td>
                <td>
                    @if(!empty($account->bic))
                        {{ format_bic($account->bic) }}
                    @else
                        <i>@lang('misc.unspecified')</i>
                    @endif
                </td>
            </tr>
            <tr>
                <td>@lang('pages.bunqAccounts.lastCheckedAt')</td>
                @if($account->checked_at != null)
                    <td>@include('includes.humanTimeDiff', ['time' => $account->checked_at])</td>
                @else
                    <td><i>@lang('misc.unknown')</i></td>
                @endif
            </tr>
            <tr>
                <td>@lang('pages.bunqAccounts.lastRenewedAt')</td>
                @if($account->renewed_at != null)
                    <td>@include('includes.humanTimeDiff', ['time' => $account->renewed_at])</td>
                @else
                    <td><i>@lang('pages.bunqAccounts.notRenewedYet')</i></td>
                @endif
            </tr>
            <tr>
                <td>@lang('misc.createdAt')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $account->created_at])</td>
            </tr>
            @if($account->created_at != $account->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $account->updated_at])</td>
                </tr>
            @endif
        </tbody>
    </table>

    <p>
        <div class="ui buttons">
            <a href="{{ route('app.bunqAccount.edit', ['accountId' => $account->id]) }}"
                    class="ui button secondary">
                @lang('misc.edit')
            </a>
            {{-- TODO: implement this --}}
            <a href="{{ route('app.bunqAccount.delete', ['accountId' => $account->id]) }}"
                    class="ui button negative disabled">
                @lang('misc.delete')
            </a>
        </div>
    </p>

    <p>
        {!! Form::open(['action' => ['AppBunqAccountController@doHousekeep', $account->id], 'method' => 'POST', 'class' => 'ui form']) !!}
            <button class="ui button orange" type="submit">@lang('pages.bunqAccounts.runHousekeeping')</button>
        {!! Form::close() !!}
    </p>

    <p>
        <a href="{{ route('app.bunqAccount.index') }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
