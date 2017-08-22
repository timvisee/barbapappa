@extends('layouts.app')

@section('content')
    <h1>@lang('pages.yourAccount')</h1>
    <p>@lang('pages.accountOverview.description')</p>

    <h3>@lang('pages.profile')</h3>
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
    <a href="{{ route('profile.edit', ['userId' => $user->id]) }}">@lang('pages.editProfile')</a><br />
    <a href="{{ route('password.change') }}">@lang('pages.changePassword')</a>

    <h3>@lang('account.email')</h3>
    <ul>
        <li>timvisee@gmail.com</li>
    </ul>
@endsection
