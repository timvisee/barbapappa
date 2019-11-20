@extends('layouts.app')

@section('title', __('pages.accountPage.email.verifyEmails'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>
        @lang('pages.accountPage.email.verifiedDescription')<br>
        <br>
        @lang('misc.emailNotReceivedCheckSpam')
    </p>

    <div class="ui hidden divider"></div>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">{{ trans_choice('pages.accountPage.email.unverified#', count($emails)) }}</h5>
        @foreach($emails as $email)
            <div class="item negative">
                {{ $email->email }}
            </div>
        @endforeach
    </div>

    <a href="{{ url()->current() }}" class="ui button primary small">
        @lang('pages.accountPage.email.iVerifiedAll')
    </a>
@endsection

