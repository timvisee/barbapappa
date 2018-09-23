@extends('layouts.app')

@section('content')
    @if($isOtherUser)
        {{-- TODO: translate this properly --}}
        <i>Note: viewing account of someone else</i>
    @endif

    <h1>@lang('pages.accountPage.email.yourEmails')</h1>
    <p>@lang('pages.accountPage.email.description')</p>

    <h3>@lang('account.emails')</h3>
    <ul>
        @forelse($user->emails()->get() as $email)
            <li>
                {{ $email->email }}

                @if(!$email->isVerified())
                    <i>@lang('misc.notVerified')</i>

                    <a href="{{ route('account.emails.reverify', ['userId' => $user->id, 'emailId' => $email->id]) }}">@lang('misc.reverify')</a>
                @endif

                <a href="{{ route('account.emails.delete', ['userId' => $user->id, 'emailId' => $email->id]) }}">@lang('misc.delete')</a>
            </li>
        @empty
            <i>@lang('account.noEmails')</i>
        @endforelse
    </ul>

    <br />
    <div data-role="controlgroup">
        <a href="{{ route('account.emails.create', ['userId' => $user->id]) }}" class="ui-btn ui-btn-corner-all">@lang('pages.accountPage.addEmail.title')</a>
    </div>
@endsection

