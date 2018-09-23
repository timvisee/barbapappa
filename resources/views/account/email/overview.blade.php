@extends('layouts.app')

@section('content')
    @if($isOtherUser)
        {{-- TODO: translate this properly --}}
        <i>Note: viewing account of someone else</i>
    @endif

    <h1>@lang('pages.yourEmails')</h1>
    {{-- <p>@lang('pages.accountOverview.description')</p> --}}

    <h3>@lang('account.unverifiedEmails')</h3>
    <ul>
        <li>timvisee@gmail.com</li>
    </ul>

    <h3>@lang('account.emails')</h3>
@endsection

