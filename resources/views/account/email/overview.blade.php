@extends('layouts.app')

@section('content')
    @if($isOtherUser)
        {{-- TODO: translate this properly --}}
        <i>Note: viewing account of someone else</i>
    @endif

    <h1>@lang('pages.yourEmails')</h1>
    {{-- <p>@lang('pages.accountOverview.description')</p> --}}

    <h3>@lang('account.emails')</h3>
    <ul>
        @forelse($user->emails()->get() as $email)
            <li>
                {{ $email->email }}

                @if(!$email->isVerified())
                    <i>@lang('misc.notVerified')</i>
                @endif
            </li>
        @empty
            <i>@lang('account.noEmails')</i>
        @endforelse
    </ul>
@endsection

