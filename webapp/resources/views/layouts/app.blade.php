<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="HandheldFriendly" content="True">
    @unless($layout_dark_mode ?? false)
        <meta name="theme-color" content="#e9e9e9">
    @else
        <meta name="theme-color" content="#1b1c1d">
    @endif
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/img/logo/logo_apple_touch_icon.png">

    <meta name="application-name" content="{{ config('app.name', 'Barbapappa') }}">
    <meta name="author" content="{{ config('app.author') }}">
    <meta name="description" content="{{ config('app.description') }}">
    <meta name="keywords" content="{{ config('app.keywords') }}">
    @unless($layout_dark_mode ?? false)
        <link rel="manifest" href="/manifest.webmanifest">
    @else
        <link rel="manifest" href="/manifest-dark.webmanifest">
    @endif

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    {{-- Title --}}
    @if(isset($breadcrumbs) && $breadcrumbs->count() > 1)
        @php
            $page_title = $breadcrumbs
                ->skip(1)
                ->reverse()
                ->map(function($breadcrumb) {
                    return $breadcrumb->title;
                })
                ->join(' / ')
                . ' - ' . config('app.name', 'Barbapappa');
        @endphp
        <title>{{ $page_title }}</title>
        <meta name="title" content="{{ $page_title }}">
    @else
        @hasSection('title')
            <title>@yield('title') - {{ config('app.name', 'Barbapappa') }}</title>
            <meta name="title" content="@yield('title') - {{ config('app.name', 'Barbapappa') }}">
        @else
            <title>{{ config('app.name', 'Barbapappa') }}</title>
            <meta name="title" content="{{ config('app.name', 'Barbapappa') }}">
        @endif
    @endif

    {{-- Styles --}}
    <link href="{{ mix('css/vendor.css') }}" rel="stylesheet" />
    <link href="{{ mix('css/app.css') }}" rel="stylesheet" />
    @stack('styles')

    {{-- Scripts --}}
    <script type="text/javascript" src="{{ mix('js/vendor.js') }}"></script>
    <script type="text/javascript" src="{{ mix('js/app.js') }}"></script>
    @stack('scripts')

    {{-- Preloads/prefetches --}}
    <link rel="prefetch" href="{{ asset('sw.js') }}" as="worker">
    <link rel="prefetch" href="{{ mix('js/widget/quickbuy.js') }}" as="script">
    <link rel="prefetch" href="{{ mix('js/widget/advancedbuy.js') }}" as="script">

</head>
<body>
    @include('includes.sidebarMainMenu')
    @include('includes.sidebarMessages')

    <div class="pusher {{ ($layout_dark_mode ?? false) ? 'inverted' : '' }}">
        @include('includes.toolbar', ['layout_dark_mode' => $layout_dark_mode ?? false])

        <div class="ui container page">
            @if(!isset($dontLeak) || !$dontLeak)
                @include('includes.impersonate')
            @endif

            {{-- Breadcrumbs --}}
            @if(isset($breadcrumbs) && $breadcrumbs->count() > 1)
                @include('partials.breadcrumbs', $breadcrumbs)
            @endif

            <div class="ui toolbar-messages">
                @include('includes.message')
                @stack('toolbar-messages')
            </div>

            @yield('content')
        </div>
    </div>
</body>
</html>
