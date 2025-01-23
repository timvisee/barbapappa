@extends('layouts.app')

@section('title', $bar->name)
@php
    $breadcrumbs = Breadcrumbs::generate('bar.show', $bar);
    $menusection = 'bar';

    use \App\Http\Controllers\BarController;
@endphp

@push('scripts')
    <script type="text/javascript">
        // Provide API base url to client-side buy widget
        var barapp_bar_url = '{{ route("bar.show", ["barId" => $bar->human_id]) }}';
        var barapp_barbuy_api_url = '{{ route("bar.buy.api", ["barId" => $bar->human_id]) }}';
    </script>

    <script type="text/javascript" src="{{ mix('js/widget/barbuy.js') }}" async></script>
@endpush

@push('toolbar-messages')
    {{-- Low balance message --}}
    @if(isset($userBalance) && $userBalance->amount < 0 && !empty($bar->low_balance_text))
        <div class="ui error message">
            <span class="halflings halflings-exclamation-sign icon"></span>
            {!! nl2br(e($bar->low_balance_text)) !!}
            <a href="{{ route('community.wallet.quickTopUp', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id
            ]) }}">@lang('pages.wallets.topUpNow')</a>
        </div>
    @endif
@endpush

@section('content')
    @include('bar.include.barHeader')
    @include('bar.include.joinBanner')

    @if($bar->enabled)
        <div id="barbuy">
            <div v-if="refreshing" class="ui active centered text loader">
                {{-- TODO: improve this message --}}
                @lang('misc.loading')...
            </div>
        </div>

        <br />
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
