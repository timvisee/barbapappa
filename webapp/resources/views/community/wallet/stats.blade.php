@extends('layouts.app')

@section('title', __('pages.walletStats.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.wallet.show', $community, $wallet);
@endphp

@push('scripts')
    <script src="{{ mix('js/vendor/chart.js') }}"></script>
@endpush

@php
    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.wallets.backToWallet'),
        'link' => route('community.wallet.show', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'walletId' => $wallet->id,
                ]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {{-- Period selector --}}
    <div class="ui three item menu">
        <a href="{{ route('community.wallet.stats', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'walletId' => $wallet->id,
                    'period' => 'week',
                ]) }}"
                class="item {{ $period == 'week' ? 'active' : '' }}">@lang('pages.walletStats.period.week')</a>
        <a href="{{ route('community.wallet.stats', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'walletId' => $wallet->id,
                    'period' => 'month',
                ]) }}"
                class="item {{ $period == 'month' ? 'active' : '' }}">@lang('pages.walletStats.period.month')</a>
        <a href="{{ route('community.wallet.stats', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'walletId' => $wallet->id,
                    'period' => 'year',
                ]) }}"
                class="item {{ $period == 'year' ? 'active' : '' }}">@lang('pages.walletStats.period.year')</a>
    </div>

    <div class="ui hidden divider"></div>

    <script>
        /**
        * A color wheel function.
        * Returns a color for an index.
        */
        function colorWheel(index, opacity = 1.0) {
            return 'rgba('
                + (Math.sin(index / (Math.PI / 1.5) + Math.PI * 2 / 3 * 0) + 1) * 255 / 2 + ', '
                + (Math.sin(index / (Math.PI / 1.5) + Math.PI * 2 / 3 * 1) + 1) * 255 / 2 + ', '
                + (Math.sin(index / (Math.PI / 1.5) + Math.PI * 2 / 3 * 2) + 1) * 255 / 2 + ', '
                + opacity
                + ')';
        };
    </script>

    <div class="ui two column stackable grid">

        <div class="column">
            <h3 class="ui horizontal divider header">@lang('pages.walletStats.title')</h3>

            <p>@lang('pages.walletStats.description')</p>

            <p>{!! $smartText !!}</p>
        </div>

        <div class="column">
            <h3 class="ui horizontal divider header">@lang('pages.walletStats.transactions')</h3>

            <div class="ui hidden divider"></div>

            <div class="ui two small statistics">
                <div class="statistic">
                    <div class="value">{{ $transactionCount }}</div>
                    <div class="label">@lang('pages.walletStats.transactions')</div>
                </div>
            </div>

            <div class="ui hidden divider"></div>

            <div class="ui two small statistics">
                <div class="statistic">
                    <div class="value">{!! $income->formatAmount(BALANCE_FORMAT_COLOR, ['neutral' => true]) !!}</div>
                    <div class="label">@lang('pages.walletStats.income')</div>
                </div>
                <div class="statistic">
                    <div class="value">{!! $paymentIncome->formatAmount(BALANCE_FORMAT_COLOR, ['neutral' => true]) !!}</div>
                    <div class="label">@lang('pages.walletStats.paymentIncome')</div>
                </div>
            </div>

            <div class="ui hidden divider"></div>

            <div class="ui two small statistics">
                <div class="statistic">
                    <div class="value">{!! $expenses->formatAmount(BALANCE_FORMAT_COLOR, ['neutral' => true]) !!}</div>
                    <div class="label">@lang('pages.walletStats.expenses')</div>
                </div>
                <div class="statistic">
                    <div class="value">{!! $productExpenses->formatAmount(BALANCE_FORMAT_COLOR, ['neutral' => true]) !!}</div>
                    <div class="label">@lang('pages.walletStats.productExpenses')</div>
                </div>
            </div>

            <div class="ui hidden divider"></div>

            <div class="ui two small statistics">
                <div class="statistic">
                    <div class="value">{{ $productCount }}</div>
                    <div class="label">@lang('pages.walletStats.products')</div>
                </div>
                <div class="statistic">
                    <div class="value">{{ $uniqueProductCount }}</div>
                    <div
                        class="label">@lang('pages.walletStats.uniqueProducts')</div>
                </div>
            </div>

        </div>

        {{-- TODO: do not show if no values --}}
        <div class="{{ $period != 'week' ? 'sixteen wide' : '' }} column">
            <h3 class="ui horizontal divider header">
                @lang('pages.walletStats.balanceHistory')
            </h3>

            <div>
                <canvas id="chartBalanceGraph"
                    height="100"
                    aria-label="@lang('misc.balance')"
                    role="img"></canvas>
                <script>
                    var data = JSON.parse('{!! json_encode($balanceGraphData) !!}');
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
                                        min: '{{ $periodFrom->subDay()->toDateString() }}',
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
                                        grid: {
                                            color: false,
                                            tickColor: 'darkgrey',
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
            </div>
        </div>

        {{-- TODO: do not show if no values --}}
        <div class="{{ $period != 'week' ? 'sixteen wide' : '' }} column">

            <h3 class="ui horizontal divider header">
                @lang('pages.walletStats.purchaseHistogram')
            </h3>

            <div>
                <canvas id="chartBuyHistogram"
                    height="100"
                    aria-label="@lang('pages.walletStats.typeProductDist.chartName')"
                    role="img"></canvas>
                <script>
                    var data = JSON.parse('{!! json_encode($buyHistogramData) !!}');
                    data.datasets[0].backgroundColor = '#3366cc';
                    data.datasets[0].borderColor = '#3366cc';
                    var chartBuyHistogram = new Chart(
                        document.getElementById('chartBuyHistogram').getContext('2d'),
                        {
                            type: 'bar',
                            data: data,
                            options: {
                                animation: false,
                                plugins: {
                                    legend: false,
                                },
                                @if($period == 'year')
                                    barPercentage: 1,
                                    categoryPercentage: 1,
                                    barThickness: 2,
                                    offset: false,
                                @endif
                                gridLines: {
                                    offsetGridLines: false,
                                },
                                scales: {
                                    x: {
                                        type: 'time',
                                        min: '{{ $periodFrom->toDateString() }}',
                                        max: '{{ today()->toDateString() }}',
                                        time: {
                                            parser: 'YYYY-MM-DD',
                                            tooltipFormat: 'll',
                                            unit: 'day',
                                            stepSize: 1,
                                            displayFormats: {
                                                day: 'll'
                                            }
                                        },
                                        grid: {
                                            color: false,
                                            tickColor: 'darkgrey',
                                        },
                                    },
                                    y: {
                                        ticks: {
                                            beginAtZero: true,
                                        }
                                    },
                                }
                            }
                        },
                    );
                </script>
            </div>

        </div>

        {{-- TODO: do not show if no values --}}
        <div class="column">

            <h3 class="ui horizontal divider header">
                @lang('pages.walletStats.purchaseDistribution')
            </h3>

            <div>
                <canvas id="chartProductDist"
                    aria-label="@lang('pages.walletStats.typeProductDist.chartName')"
                    role="img"></canvas>
                <script>
                    var data = JSON.parse('{!! json_encode($productDistData) !!}');
                    data.datasets[0].backgroundColor = function(context) {
                            return colorWheel(context.dataIndex, 0.5);
                        };
                    data.datasets[0].borderColor = function(context) {
                            return colorWheel(context.dataIndex, 0.8);
                        };
                    data.datasets[0].hoverBackgroundColor = function(context) {
                            return colorWheel(context.dataIndex, 0.8);
                        };
                    data.datasets[0].hoverBorderColor = function(context) {
                            return colorWheel(context.dataIndex, 1);
                        };
                    var chartProductDist = new Chart(
                        document.getElementById('chartProductDist').getContext('2d'),
                        {
                            type: 'doughnut',
                            data: data,
                            options: {
                                animation: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                    },
                                },
                            },
                        }
                    );
                </script>
            </div>

        </div>

        {{-- TODO: do not show if no values --}}
        <div class="column">

            <h3 class="ui horizontal divider header">
                @lang('pages.walletStats.purchasePerDay')
            </h3>

            <div>
                <canvas id="chartBuyTimeDay"
                    height="125"
                    aria-label="@lang('pages.walletStats.typeProductDist.chartName')"
                    role="img"></canvas>
                <script>
                    var data = JSON.parse('{!! json_encode($buyTimeDayData) !!}');
                    data.datasets[0].backgroundColor = function(context) {
                            return colorWheel(context.dataIndex, 0.5);
                        };
                    data.datasets[0].borderColor = function(context) {
                            return colorWheel(context.dataIndex, 0.8);
                        };
                    data.datasets[0].hoverBackgroundColor = function(context) {
                            return colorWheel(context.dataIndex, 0.8);
                        };
                    data.datasets[0].hoverBorderColor = function(context) {
                            return colorWheel(context.dataIndex, 1);
                        };
                    var chartBuyTimeDay = new Chart(
                        document.getElementById('chartBuyTimeDay').getContext('2d'),
                        {
                            type: 'bar',
                            data: data,
                            options: {
                                animation: false,
                                plugins: {
                                    legend: false,
                                },
                                scales: {
                                    y: {
                                        ticks: {
                                            beginAtZero: true,
                                            precision: 0,
                                        },
                                    },
                                }
                            }
                        },
                    );
                </script>
            </div>

            <h3 class="ui horizontal divider header">
                @lang('pages.walletStats.purchasePerHour')
            </h3>

            <div>
                <canvas id="chartBuyTimeHour"
                    height="125"
                    aria-label="@lang('pages.walletStats.typeProductDist.chartName')"
                    role="img"></canvas>
                <script>
                    var data = JSON.parse('{!! json_encode($buyTimeHourData) !!}');
                    data.datasets[0].backgroundColor = function(context) {
                            return colorWheel(context.dataIndex, 0.5);
                        };
                    data.datasets[0].borderColor = function(context) {
                            return colorWheel(context.dataIndex, 0.8);
                        };
                    data.datasets[0].hoverBackgroundColor = function(context) {
                            return colorWheel(context.dataIndex, 0.8);
                        };
                    data.datasets[0].hoverBorderColor = function(context) {
                            return colorWheel(context.dataIndex, 1);
                        };
                    var chartBuyTimeHour = new Chart(
                        document.getElementById('chartBuyTimeHour').getContext('2d'),
                        {
                            type: 'bar',
                            data: data,
                            options: {
                                animation: false,
                                plugins: {
                                    legend: false,
                                },
                                scales: {
                                    y: {
                                        ticks: {
                                            beginAtZero: true,
                                            precision: 0,
                                        },
                                    },
                                }
                            }
                        },
                    );
                </script>
            </div>

        </div>

    </div>

    <div class="ui hidden divider"></div>

    <p>
        <a href="{{ route('community.wallet.show', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'walletId' => $wallet->id
                ]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
