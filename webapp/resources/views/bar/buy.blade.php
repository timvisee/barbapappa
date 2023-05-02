@extends('layouts.app')

@section('title', $bar->name)
@php
    $breadcrumbs = Breadcrumbs::generate('bar.show', $bar);
    $menusection = 'bar';
@endphp

@push('scripts')
    <script type="text/javascript">
        // Provide API base url to client-side buy widget
        var barapp_advancedbuy_api_url = '{{ route("bar.buy.api", ["barId" => $bar->human_id]) }}';
    </script>

    <script type="text/javascript" src="{{ mix('js/widget/advancedbuy.js') }}" async></script>
@endpush

@section('content')
    {{-- Low balance message --}}
    @if(isset($userBalance) && $userBalance->amount < 0 && !empty($bar->low_balance_text))
        <div class="ui error message attach-toolbar">
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

    @if($bar->enabled)
        <div class="ui two item menu">
            <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}" class="item">@lang('pages.bar.buy.forMe')</a>
            <a href="{{ route('bar.buy', ['barId' => $bar->human_id]) }}" class="item active">@lang('pages.bar.buy.forOthers')</a>
        </div>

        <div id="advancedbuy">
            <div class="ui active centered large loader"></div>
        </div>

        <br>
        <br>
    @else
        <div class="ui warning message">
            <span class="halflings halflings-warning-sign icon"></span>
            @lang('pages.bar.disabledGotoDashboard')
        </div>

        <a href="{{ route('dashboard') }}"
                class="ui button basic">
            @lang('pages.dashboard.backToDashboard')
        </a>
    @endif
@endsection
