@extends('layouts.app')

@section('title', __('pages.bar.tallySummary'))

@push('styles')
    <style>
        .center {
            text-align: center;
        }

        /* TODO: a hack to center toolbar logo, fix this */
        .toolbar-logo {
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
        }
    </style>
@endpush

@section('content')

    <div class="center">
        <a href="{{ route('kiosk.main') }}"
                    class="ui big basic button center aligned">
            @lang('pages.kiosk.backToKiosk')
        </a>
    </div>

    <h2 class="ui header center aligned">@yield('title')</h2>

    @if($tallies->isNotEmpty() && $showingLimited)
        <div class="ui large warning message">
            <span class="halflings halflings-warning-sign icon"></span>
            @lang('pages.bar.tallySummaryLimited')
        </div>
    @endif

    <div class="ui top vertical huge menu fluid">
        <h5 class="ui item header">
            @lang('pages.bar.tallySummaryDescriptionSum', [
                'quantity' => $quantity,
                'from' => $timeFrom->longRelativeDiffForHumans(null, null),
            ]):
        </h5>

        @forelse($tallies as $userTally)
            <div class="item">
                {{ $userTally['owner']?->name ?? __('misc.unknownUser') }}
                <span class="subtle">{{ $userTally['quantity'] }}×</span>

                <span style="float: right; font-weight: bold;">
                    @for($i = 0; $i < $userTally['quantity'] % 5; $i += 1)|@endfor
                    @for($i = 0; $i < floor($userTally['quantity'] / 5); $i += 1)
                        <s>|||||</s>
                    @endfor
                </span>
            </div>

        @empty
            <i class="item">@lang('pages.bar.noPurchases')...</i>
        @endforelse
    </div>
@endsection
