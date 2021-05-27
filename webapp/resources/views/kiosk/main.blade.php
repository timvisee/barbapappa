@extends('layouts.app')

@section('title', $bar->name)

@push('scripts')
    <script type="text/javascript" src="{{ asset('js/advancedbuy.js') }}"></script>
@endpush

@section('content')
    <h2 class="ui header bar-header">
        <div>
            @yield('title')
        </div>
    </h2>

    <div id="advancedbuy">
        <div class="ui active centered inline loader"></div>
    </div>
    <br>
@endsection
