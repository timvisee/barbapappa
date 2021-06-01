<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="HandheldFriendly" content="True">
    <meta name="theme-color" content="#e9e9e9">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="shortcut apple-touch-icon" type="image/png" href="/img/logo/logo_apple_touch_icon.png">

    <meta name="application-name" content="{{ config('app.name', 'Barbapappa') }}">
    <meta name="author" content="{{ config('app.author') }}">
    <meta name="description" content="{{ config('app.description') }}">
    <meta name="keywords" content="{{ config('app.keywords') }}">
    <link rel="manifest" href="/manifest.webmanifest">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    {{-- Title --}}
    @hasSection('title')
        <title>@yield('title') - {{ config('app.name', 'Barbapappa') }}</title>
        <meta name="title" content="@yield('title') - {{ config('app.name', 'Barbapappa') }}">
    @else
        <title>{{ config('app.name', 'Barbapappa') }}</title>
        <meta name="title" content="{{ config('app.name', 'Barbapappa') }}">
    @endif

    {{-- Styles --}}
    <link href="{{ mix('css/vendor.css') }}" rel="stylesheet" />
    <link href="{{ mix('css/app.css') }}" rel="stylesheet" />

    {{-- Scripts --}}
    <script type="text/javascript" src="{{ mix('js/vendor.js') }}"></script>
    <script type="text/javascript" src="{{ mix('js/app.js') }}"></script>

    @stack('styles')
    @stack('scripts')

</head>
<body>
    @include('includes.sidebarMainMenu')
    @include('includes.sidebarMessages')

    <div class="pusher">
        @include('includes.toolbar')

        <div class="ui container page">
            @if(!isset($dontLeak) || !$dontLeak)
                @include('includes.impersonate')
            @endif

            @include('includes.message')

            @yield('content')
        </div>
    </div>
</body>
</html>
