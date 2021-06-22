@extends('layouts.app')

@section('title', __('pages.accountPage.email.unverifiedEmails'))
@php
    $breadcrumbs = Breadcrumbs::generate('account.emails', $user);
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.accountPage.email.unverifiedDescription')</p>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">{{ trans_choice('pages.accountPage.email.unverified#', count($emails)) }}</h5>
        @foreach($emails as $email)
            <div class="item">
                {{ $email->email }}

                @if($anyVerified)
                    <a href="{{ route('account.emails.delete', [
                                'userId' => $user->id,
                                'emailId' => $email->id
                            ]) }}"
                            class="ui button basic label">
                        @lang('misc.delete')
                    </a>
                @endif
            </div>
        @endforeach
    </div>

    {!! Form::open(['action' => [
        'EmailController@doVerifyAll',
        $user->id,
    ], 'method' => 'POST', 'class' => 'display-inline']) !!}
        <button class="ui button primary"
            type="submit">{{ trans_choice('pages.accountPage.email.verify#', count($emails)) }}</button>
    {!! Form::close() !!}

    <a href="{{ route('account', ['userId' => $user->id]) }}"
            class="ui button basic">
        @lang('pages.accountPage.email.backToEmails')
    </a>
@endsection

