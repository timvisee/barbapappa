@extends('layouts.app')

@section('title', __('pages.walletStats.title'))

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
    <h2 class="ui header">
        @yield('title')

        <div class="sub header">
            @lang('misc.in')
            <a href="{{ route('community.wallet.show', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'walletId' => $wallet->id
                ]) }}">
                {{ $wallet->name }}
            </a>
            @lang('misc.for')
            <a href="{{ route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}">
                {{ $economy->name }}
            </a>
        </div>
    </h2>

    <p>@lang('pages.walletStats.description')</p>

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

        {{-- TODO: do not show if no values --}}
        <div class="column">
            <div class="ui segment">

                <h3 class="ui header">@lang('pages.walletStats.purchases')</h3>

                <div class="ui hidden divider"></div>

                <div class="ui two small statistics">
                    <div class="statistic">
                        <div class="value">{{ $productsBought }}</div>
                        <div class="label">@lang('pages.walletStats.products')</div>
                    </div>
                    <div class="statistic">
                        <div class="value">{{ $differentProducts }}</div>
                        <div
                            class="label">@lang('pages.walletStats.differentProducts')</div>
                    </div>
                </div>

            </div>
        </div>

        {{-- TODO: do not show if no values --}}
        <div class="column">
            <div class="ui segment">

                <h3 class="ui header">
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
        </div>

        {{-- TODO: do not show if no values --}}
        <div class="column">
            <div class="ui segment">

                <h3 class="ui header">
                    @lang('pages.walletStats.purchasePerHourDay')
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

            </div>
        </div>

        {{-- TODO: do not show if no values --}}
        <div class="column">
            <div class="ui segment">

                <h3 class="ui header">
                    @lang('pages.walletStats.purchaseHistogram')
                </h3>

                <div>
                    <canvas id="chartBuyHistogram"
                        height="125"
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
                                    barPercentage: 1.3,
                                    categoryPercentage: 1.3,
                                    offset: false,
                                    gridLines: {
                                        offsetGridLines: false,
                                    },
                                    scales: {
                                        x: {
                                            type: 'time',
                                            time: {
                                                parser: 'YYYY-MM-DD',
                                                tooltipFormat: 'll',
                                                unit: 'day',
                                                unitStepSize: 1,
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
        </div>
    </div>

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
