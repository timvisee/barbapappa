@extends('layouts.app')

@section('title', __('pages.walletStats.title'))

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

    <h3 class="ui horizontal divider header">
        @lang('pages.walletStats.typeProductDist.title')
    </h3>

    <canvas id="chartProductDist"
        height="150"
        aria-label="@lang('pages.walletStats.typeProductDist.chartName')"
        role="img"></canvas>
    <script>
        var data = JSON.parse('{!! json_encode($productDistData) !!}');
        data.datasets[0].backgroundColor = function(context) {
                var index = context.dataIndex;
                return colorWheel(index, 0.5);
            };
        data.datasets[0].borderColor = function(context) {
                var index = context.dataIndex;
                return colorWheel(index, 0.8);
            };
        data.datasets[0].hoverBackgroundColor = function(context) {
                var index = context.dataIndex;
                return colorWheel(index, 0.8);
            };
        data.datasets[0].hoverBorderColor = function(context) {
                var index = context.dataIndex;
                return colorWheel(index, 1);
            };
        var chartProductDist = new Chart(
            document.getElementById('chartProductDist').getContext('2d'),
            {
                type: 'doughnut',
                data: data,
            }
        );
    </script>

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
