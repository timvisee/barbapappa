@extends('layouts.app')

@section('title', __('misc.kiosk') . ': ' . $bar->name)

@push('scripts')
    <script type="text/javascript">
        // Provide API base url to client-side buy widget
        var barapp_kioskbuy_api_url = '{{ route("kiosk.api") }}';
    </script>

    <script type="text/javascript" src="{{ mix('js/widget/kioskbuy.js') }}" async></script>
@endpush

@section('content')
    <div id="kioskbuy">
        <div v-if="refreshing" class="ui active centered large text loader">
            @lang('pages.kiosk.loading')...
        </div>
    </div>
@endsection
