@extends('layouts.app')

@section('title', __('pages.accountPage.email.yourEmails'))
@php
    $breadcrumbs = Breadcrumbs::generate('account.emails', $user);
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.accountPage.email.description')</p>

    @php
        // Count configured and unverified email addresses
        $mailsConfigured = $user->emails()->count();
        $mailsUnverified = $user->emails()->unverified()->count();
    @endphp

    <table class="ui compact celled table">
        <thead>
            <tr>
                <th>@lang('account.email')</th>
                <th>@lang('misc.actions')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($user->emails()->get() as $email)
                <tr class="{{ $email->isVerified() ? '' : 'negative' }}">
                    <td>
                        {{ $email->email }}
                    </td>
                    <td>
                        @if(!$email->isVerified())
                            {!! Form::open(['action' => [
                                'EmailController@reverify',
                                $user->id,
                                'emailId' => $email->id
                            ], 'method' => 'POST', 'class' => 'display-inline']) !!}
                                <button class="ui button small secondary" type="submit">
                                    @lang('pages.accountPage.email.resendVerify')
                                </button>
                            {!! Form::close() !!}
                        @endif

                        <a href="{{ route('account.emails.delete', ['userId' => $user->id, 'emailId' => $email->id]) }}" class="ui button small basic">@lang('misc.delete')</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="full-width">
            <tr>
                <th colspan="2">
                    <a href="{{ route('account.emails.create', ['userId' => $user->id]) }}" class="ui button small primary">@lang('pages.accountPage.addEmail.title')</a>
                </th>
            </tr>
        </tfoot>
    </table>

    <div class="ui divider hidden"></div>

    <h2 class="ui header">@lang('misc.otherSettings')</h2>
    {!! Form::open(['action' => ['EmailController@update', $user->id], 'method' => 'PUT', 'class' => 'ui form']) !!}
        <div class="inline field {{ ErrorRenderer::hasError('mail_receipt') ?  'error' : '' }}">
            <div class="ui toggle checkbox">
                {{ Form::checkbox('mail_receipt', true, $user->mail_receipt, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('mail_receipt', __('pages.accountPage.email.mailReceipt')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('mail_receipt') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('notify_low_balance') ?  'error' : '' }}">
            <div class="ui toggle checkbox">
                {{ Form::checkbox('notify_low_balance', true, $user->notify_low_balance, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('notify_low_balance', __('pages.accountPage.email.notifyOnLowBalance')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('notify_low_balance') }}
        </div>

        <button class="ui button primary" type="submit">@lang('misc.saveChanges')</button>
    {!! Form::close() !!}

    <div class="ui divider hidden"></div>

    <a href="{{ route('account', ['userId' => $user->id]) }}"
            class="ui button basic">
        @lang('pages.accountPage.backToAccount')
    </a>
@endsection

