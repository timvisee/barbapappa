@extends('layouts.app')

@section('title', $wallet->name)
@php
    $breadcrumbs = Breadcrumbs::generate('community.wallet.show', $community, $wallet);
    $menusection = 'community';

    use App\Perms\CommunityRoles;
@endphp

@push('scripts')
    <script src="{{ mix('js/vendor/chart.js') }}"></script>
@endpush

@section('content')
    <h2 class="ui header">@yield('title')</h2>

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

    @if($balance_graph_data)
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
                        plugins: {
                            legend: false,
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return '{{ $wallet->currency->symbol }} ' + context.parsed.y.toFixed(2);
                                    }
                                }
                            }
                        },
                        borderCapStyle: 'round',
                        borderJoinStyle: 'round',
                        cubicInterpolationMode: 'monotone',
                        fill: true,
                        scales: {
                            x: {
                                max: '{{ today()->toDateString() }}',
                                type: 'time',
                                time: {
                                    tooltipFormat: 'll',
                                    unit: 'day',
                                    stepSize: 1,
                                    displayFormats: {
                                        day: 'll'
                                    },
                                },
                                ticks: {
                                    display: false,
                                },
                                grid: {
                                    tickColor: false,
                                },
                            },
                            y: {
                                ticks: {
                                    callback: function(value, index, values) {
                                        return  '{{ $wallet->currency->symbol }} ' + value;
                                    }
                                }
                            },
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
                        class="item disabled">
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
