@extends('layouts.app')

@section('content')
    @if($isOtherUser)
        {{-- TODO: translate this properly --}}
        <i>Note: viewing account of someone else</i>
    @endif

    <h1>@lang('pages.yourAccount')</h1>
    <p>@lang('pages.accountPage.description')</p>

    <h3>@lang('pages.profile.name')</h3>
    <table>
        <tr>
            <td>@lang('account.firstName'):</td>
            <td>{{ $user->first_name }}</td>
        </tr>
        <tr>
            <td>@lang('account.lastName'):</td>
            <td>{{ $user->last_name }}</td>
        </tr>
        <tr>
            <td>@lang('lang.language'):</td>
            <td>{{ $user->locale != null ? __('lang.name', [], $user->locale) : __('misc.unspecified') }}</td>
        </tr>
    </table>
    <a href="{{ route('profile.edit', ['userId' => $user->id]) }}">@lang('pages.editProfile.name')</a><br />
    <a href="{{ route('password.change') }}">@lang('pages.changePassword')</a>

    <h3>@lang('account.email')</h3>
    @php
        // Count configured and unverified email addresses
        $mailsConfigured = $user->emails()->count();
        $mailsUnverified = $user->emails()->where('verified_at', null)->count();
    @endphp
    @if($mailsUnverified > 0)
        {{ $mailsUnverified }} @lang('misc.unverified'), 
    @endif
    {{ $mailsConfigured }} configured<br />
    <a href="{{ route('account.emails', ['userId' => $user->id]) }}">@lang('account.manageEmails')</a>
@endsection
