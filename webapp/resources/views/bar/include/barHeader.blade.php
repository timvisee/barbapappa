{{-- Request email verification if not verified after a week --}}
@if($mustVerify)
    <div class="ui warning message">
        <span class="halflings halflings-warning-sign icon"></span>
        @lang('auth.mustVerifyEmail')
        <a href="{{ route('account.user.emails.unverified', [
            'userId' => barauth()->getUser()->id,
        ]) }}">@lang('misc.verifyNow')</a>.
    </div>
@endif

<h2 class="ui header bar-header">
    <div>
        @if(isset($title))
            {{ $title }}
        @else
            @yield('title')
        @endif
    </div>

    @if($joined || $economy->userHasBalance())
        @if(isset($userBalance))
            <div>
                {{-- Quick top-up if user has negative balance --}}
                @if($userBalance->amount < 0)
                    <a href="{{ route('community.wallet.quickTopUp', [
                                'communityId' => $community->human_id,
                                'economyId' => $economy->id
                            ]) }}"
                            class="ui right pointing label red label-top-up">
                        <u>@lang('pages.wallets.topUpNow')</u>
                    </a>
                @else
                    <a href="{{ route('community.wallet.quickShow', [
                                'communityId' => $community->human_id,
                                'economyId' => $economy->id
                            ]) }}"
                            class="ui small right pointing label basic label-top-up">
                        <u class="subtle">@lang('misc.details')</u>
                    </a>
                @endif

                {{-- Balance label --}}
                <a href="{{ route('community.wallet.quickShow', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                        ]) }}"
                        class="balance">
                    {!! $userBalance->formatAmount(BALANCE_FORMAT_LABEL) !!}
                </a>
            </div>
        @endif
    @endif
</h2>
