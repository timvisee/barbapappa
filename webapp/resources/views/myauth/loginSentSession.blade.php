@extends('layouts.app')

@section('title', __('auth.loginEmail'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <div class="ui success message">
        <span class="halflings halflings-ok-sign icon"></span>
        @lang('auth.sessionLinkSent', ['email' => $email->email])
    </div>

    <p>@lang('misc.emailNotReceivedCheckSpam')</p>

    <div class="ui hidden divider"></div>

    <div class="ui link list">
        @if($loginWithPassword ?? false)
            <a href="{{ route('login') }}" class="item">
                @lang('auth.loginPassword')
            </a>
        @endif
        <a href="{{ route('contact') }}" class="item">
            @lang('auth.loginTroubleContact')
        </a>
        <a href="{{ route('index') }}" class="item">
            @lang('pages.index.backToIndex')
        </a>
    </div>
@endsection
