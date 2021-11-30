@extends('layouts.app')

{{-- TODO: translate --}}
@section('title', __('pages.kioskJoin.joinBar', ['bar' => $bar->name]))

@push('styles')
    <style>
        .bar-large-qr {
            max-width: 60vmin;
            max-height: 60vmin;
            image-rendering: crisp-edges;
            image-rendering: pixelated;
            text-align: center;
        }

        .center {
            text-align: center;
        }

        .bigger {
            font-size: 1.28571429rem;
            text-align: center;
        }

        .bigger table {
            margin: auto !important;
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

    <div class="bigger center aligned">
        <p>@lang('pages.kioskJoin.description')</p>

        <p>@lang('pages.kioskJoin.scanQr')</p>

        <p class="">
            <img class="bar-large-qr" src="data:image/png;base64,{{ base64_encode(
                QrCode::format('png')
                    ->size(700)
                    ->margin(1)
                    ->errorCorrection('Q')
                    ->generate($qr_url)
                ) }}">
        </p>

        <p>@lang('pages.kioskJoin.orUrl')</p>

        <div class="ui hidden divider"></div>

        <table class="ui very basic collapsing table">
            <tbody>
                <tr>
                    <td>@lang('misc.link'):</td>
                    <td>
                        <code class="literal">{{ route('bar.join', ['barId' => $bar->human_id]) }}</code>
                    </td>
                </tr>
                @if($bar->password)
                    <tr>
                        <td>@lang('misc.code'):</td>
                        <td>
                            <code class="literal">{{ $bar->password }}</code>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

    </div>

@endsection
