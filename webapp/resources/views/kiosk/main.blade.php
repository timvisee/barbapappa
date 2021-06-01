@extends('layouts.app')

@section('title', __('misc.kiosk') . ': ' . $bar->name)

@push('scripts')
    <script type="text/javascript" src="{{ mix('js/kioskbuy.js') }}"></script>
@endpush

@section('content')
    <h2 class="ui header center aligned">
        @yield('title')
    </h2>

    <div id="kioskbuy">
        <div class="ui active centered inline loader"></div>
    </div>
@endsection
