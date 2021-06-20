@extends('layouts.app')

@section('title', $bar->name)
@php
    $breadcrumbs = Breadcrumbs::generate('bar.show', $bar);
@endphp

@push('scripts')
    <script type="text/javascript">
        // Provide API base url to client-side buy widget
        var barapp_advancedbuy_api_url = '{{ route("bar.buy.api", ["barId" => $bar->human_id]) }}';
    </script>

    <script type="text/javascript" src="{{ mix('js/widget/advancedbuy.js') }}" async></script>
@endpush

@php
    use \App\Http\Controllers\BarController;
    use \App\Http\Controllers\BarMemberController;

    // Define menulinks
    $menulinks[] = [
        'name' => __('misc.information'),
        'link' => route('bar.info', ['barId' => $bar->human_id]),
        'icon' => 'info-sign',
    ];

    if(perms(BarController::permsUser()))
        $menulinks[] = [
            'name' => __('pages.stats.title'),
            'link' => route('bar.stats', ['barId' => $bar->human_id]),
            'icon' => 'stats',
        ];

    if($joined)
        $menulinks[] = [
            'name' => __('pages.wallets.myWallets'),
            'link' => route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $bar->economy_id]),
            'icon' => 'wallet',
        ];

    if(perms(BarController::permsManage()))
        $menulinks[] = [
            'name' => __('misc.manage'),
            'link' => route('bar.manage', ['barId' => $bar->human_id]),
            'icon' => 'edit',
        ];

    $menulinks[] = [
        'name' => __('pages.community.viewCommunity'),
        'link' => route('community.show', ['communityId' => $community->human_id]),
        'icon' => 'group',
    ];
@endphp

@section('content')
    {{-- Low balance message --}}
    @if(isset($userBalance) && $userBalance->amount < 0 && !empty($bar->low_balance_text))
        <div class="ui error message">
            <span class="halflings halflings-exclamation-sign icon"></span>
            {!! nl2br(e($bar->low_balance_text)) !!}
            <a href="{{ route('community.wallet.quickTopUp', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id
            ]) }}">@lang('pages.wallets.topUpNow')</a>.
        </div>
    @endif

    @include('bar.include.barHeader')
    @include('bar.include.joinBanner')

    <div class="ui two item menu">
        <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}" class="item">@lang('pages.bar.buy.forMe')</a>
        <a href="{{ route('bar.buy', ['barId' => $bar->human_id]) }}" class="item active">@lang('pages.bar.buy.forOthers')</a>
    </div>

    <div id="advancedbuy">
        <div class="ui active centered large loader"></div>
    </div>

    <br>
    <br>
@endsection
