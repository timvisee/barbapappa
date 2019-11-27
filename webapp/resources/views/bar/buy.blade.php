@extends('layouts.app')

@section('title', $bar->name)

@push('scripts')
    <script type="text/javascript" src="{{ asset('js/advancedbuy.js') }}"></script>
@endpush

@php
    $menulinks[] = [
        'name' => __('pages.bar.backToBar'),
        'link' => route('bar.show', ['barId' => $bar->human_id]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    @include('bar.include.barHeader')
    @include('bar.include.joinBanner')

    <div id="advancedbuy">
        <div class="ui active centered inline loader"></div>
    </div>
    <br>

    <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}"
            class="ui button basic">
        @lang('pages.bar.backToBar')
    </a>
@endsection
