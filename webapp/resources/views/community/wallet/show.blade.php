@extends('layouts.app')

@section('title', $wallet->name)

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>
@endpush
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.css">
@endpush

@php
    use App\Perms\CommunityRoles;

    // Define menulinks
    $menulinks[] = [
        'name' => __('general.goBack'),
        'link' => route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $economy->id]),
        'icon' => 'undo',
    ];
    $menulinks[] = [
        'name' => __('misc.topUp'),
        'link' => route('community.wallet.topUp', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'walletId' => $wallet->id]),
        'icon' => 'credit-card',
    ];
    $menulinks[] = [
        'name' => __('pages.wallets.transfer'),
        'link' => route('community.wallet.transfer', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'walletId' => $wallet->id]),
        'icon' => 'transfer',
    ];
    $menulinks[] = [
        'name' => __('pages.transactions.title'),
        'link' => route('community.wallet.transactions', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'walletId' => $wallet->id
            ]),
        'icon' => 'fees-payments',
    ];
@endphp

@section('content')
    <h2 class="ui header">
        @yield('title')

        <div class="sub header">
            @lang('misc.in')
            <a href="{{ route('community.wallet.index', ['communityId' => $community->human_id]) }}">
                {{ $community->name }}
            </a>
            @lang('misc.for')
            <a href="{{ route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}">
                {{ $economy->name }}
            </a>
        </div>
    </h2>

    <div class="ui divider hidden"></div>

    <div class="ui one small statistics">
        <div class="statistic">
            <div class="value">
                {!! $wallet->formatBalance(BALANCE_FORMAT_COLOR) !!}
            </div>
            <div class="label">@lang('misc.balance')</div>
        </div>
    </div>

    <div class="ui divider hidden"></div>

    <center>
        <div class="ui buttons">
            <a href="{{ route('community.wallet.topUp', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'walletId' => $wallet->id]) }}"
                    class="ui button green">
                @lang('misc.pay')
            </a>
            <a href="{{ route('community.wallet.stats', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'walletId' => $wallet->id]) }}"
                    class="ui button primary">
                @lang('misc.stats')
            </a>
        </div>
    </center>

    <div class="ui divider hidden"></div>

    @if($balance_graph_data != null)
        <canvas id="chartBalanceGraph"
            height="50"
            aria-label="@lang('misc.balance')"
            role="img"></canvas>
        <script>
            var data = JSON.parse('{!! json_encode($balance_graph_data) !!}');
            data.datasets[0].borderColor = 'rgb(75, 192, 192)';
            data.datasets[0].backgroundColor = 'rgba(75, 192, 192, 0.5)';
            new Chart(
                document.getElementById('chartBalanceGraph').getContext('2d'),
                {
                    type: 'line',
                    data: data,
                    options: {
                        animation: false,
                        legend: false,
                        scales: {
                            xAxes: [{
                                display: false,
                            }],
                            yAxes: [{
                                ticks: {
                                    callback: function(value, index, values) {
                                        return '€ ' + value;
                                    }
                                }
                            }]
                        }
                    }
                },
            );
        </script>

        <div class="ui divider hidden"></div>
    @endif

    {{-- Transaction list --}}
    @include('transaction.include.list', [
        'groups' => [[
            'header' => trans_choice('pages.transactions.last#', count($transactions)),
            'transactions' => $transactions,
        ]],
        'button' => [
            'label' => __('misc.showAll'),
            'link' => route('community.wallet.transactions', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'walletId' => $wallet->id,
            ]),
        ],
    ])

    <p>
        <div class="ui floating right labeled icon dropdown button">
            <i class="dropdown icon"></i>
            @lang('misc.manage')
            <div class="menu">
                <a href="{{ route('community.wallet.edit', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'walletId' => $wallet->id]) }}"
                        class="item">
                    @lang('misc.rename')
                </a>
                <a href="{{ route('community.wallet.delete', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'walletId' => $wallet->id]) }}"
                        class="item">
                    @lang('misc.delete')
                </a>
                <div class="divider"></div>
                <a href="{{ route('community.wallet.transfer', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'walletId' => $wallet->id]) }}"
                        class="item">
                    @lang('pages.wallets.transferToSelf')
                </a>
                <a href="{{ route('community.wallet.transfer.user', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'walletId' => $wallet->id]) }}"
                        class="item">
                    @lang('pages.wallets.transferToUser')
                </a>
                <div class="divider"></div>
                <a href="{{ route('community.wallet.merge', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                        class="item">
                    @lang('misc.merge')
                </a>
            </div>
        </div>

        @if(perms(CommunityRoles::presetManager()))
            <div class="ui floating right labeled icon dropdown button">
                <i class="dropdown icon"></i>
                @lang('misc.admin')
                <div class="menu">
                    <a href="{{ route('community.wallet.modifyBalance', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'walletId' => $wallet->id]) }}"
                            class="item">
                        @lang('pages.wallets.modifyBalance')
                    </a>
                </div>
            </div>
        @endif

        <a href="{{ route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>

    <div class="ui fluid accordion">
        <div class="title">
            <i class="dropdown icon"></i>
            @lang('misc.details')
        </div>
        <div class="content">
            <table class="ui compact celled definition table">
                <tbody>
                    <tr>
                        <td>@lang('misc.name')</td>
                        <td>{{ $wallet->name }}</td>
                    </tr>
                    <tr>
                        <td>@lang('misc.balance')</td>
                        <td>{!! $wallet->formatBalance(BALANCE_FORMAT_COLOR) !!}</td>
                    </tr>
                    <tr>
                        <td>@lang('misc.owner')</td>
                        <td>{{ $wallet->economyMember->name }}</td>
                    </tr>
                    <tr>
                        <td>@lang('misc.createdAt')</td>
                        <td>@include('includes.humanTimeDiff', ['time' => $wallet->created_at])</td>
                    </tr>
                    @if($wallet->created_at != $wallet->updated_at)
                        <tr>
                            <td>@lang('misc.lastChanged')</td>
                            <td>@include('includes.humanTimeDiff', ['time' => $wallet->updated_at])</td>
                        </tr>
                    @endif
                    <tr>
                        <td>@lang('misc.reference')</td>
                        <td><code class="literal">wallet#{{ $wallet->id }}</code></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
