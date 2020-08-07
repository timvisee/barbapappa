@extends('layouts.app')

@section('title', __('pages.yourAccount'))

@php
    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.editProfile.name'),
        'link' => route('profile.edit', ['userId' => $user->id]),
        'icon' => 'edit',
    ];
    $menulinks[] = [
        'name' => __('pages.changePassword'),
        'link' => route('password.change'),
        'icon' => 'rotation-lock',
    ];
    $menulinks[] = [
        'name' => __('account.manageEmails'),
        'link' => route('account.emails', ['userId' => $user->id]),
        'icon' => 'envelope',
    ];
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.accountPage.description')</p>

    <h3>@lang('pages.profile.name')</h3>
    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('account.firstName')</td>
                <td>{{ $user->first_name }}</td>
            </tr>
            <tr>
                <td>@lang('account.lastName')</td>
                <td>{{ $user->last_name }}</td>
            </tr>
            <tr>
                <td>@lang('lang.language')</td>
                <td>{{ $user->locale != null ? __('lang.name', [], $user->locale) : __('misc.unspecified') }}</td>
            </tr>
        </tbody>
        <tfoot class="full-width">
            <tr>
                <th></th>
                <th>
                    <a href="{{ route('profile.edit', ['userId' => $user->id]) }}" class="ui button small">@lang('pages.editProfile.name')</a>
                    <a href="{{ route('password.change') }}" class="ui button small">@lang('pages.changePassword')</a>
                </th>
            </tr>
        </tfoot>
    </table>

    <h3>@lang('account.email')</h3>
    @php
        // Count configured and unverified email addresses
        $mailsConfigured = $user->emails()->count();
        $mailsUnverified = $user->emails()->unverified()->count();
    @endphp
    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('pages.accountPage.email.yourEmails')</td>
                <td>
                    <div class="ui bulleted list">
                        @foreach($user->emails()->get() as $email)
                            <div class="item">{{ $email->email }}</div>
                        @endforeach
                    </div>
                </td>
            </tr>
            @if($mailsUnverified > 0)
                <tr>
                    <td>@lang('misc.unverified')</td>
                    <td class="negative">
                        <div class="ui bulleted list">
                            @foreach($user->emails()->unverified()->get() as $email)
                                <div class="item">{{ $email->email }}</div>
                            @endforeach
                        </div>
                    </td>
                </tr>
            @endif
        </tbody>
        <tfoot class="full-width">
            <tr>
                <th></th>
                <th>
                    <a href="{{ route('account.emails', ['userId' => $user->id]) }}" class="ui button small">@lang('account.manageEmails')</a>
                </th>
            </tr>
        </tfoot>
    </table>
@endsection
