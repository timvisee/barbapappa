@extends('layouts.app')

@section('title', $bar->name)

@php
    $menulinks[] = [
        'name' => __('pages.bar.backToBar'),
        'link' => route('bar.show', ['barId' => $bar->human_id]),
        'icon' => 'undo',
    ];
@endphp

@push('scripts')
    {{-- TODO: is this required when compiling javascript files? --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/vue"></script> --}}
    <script type="text/javascript" src="{{ asset('js/buy.js') }}"></script>
@endpush

@section('content')
    @include('bar.include.barHeader')
    @include('bar.include.joinBanner')

    <div id="app">
        <div class="ui active centered inline loader"></div>
    </div>
    <br>

    <p>
        <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}"
                class="ui button basic">
            @lang('pages.bar.backToBar')
        </a>
    </p>
@endsection
