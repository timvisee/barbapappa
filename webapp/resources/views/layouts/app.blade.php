<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    {{-- Title --}}
    @hasSection('title')
        <title>@yield('title') - {{ config('app.name', 'Barbapappa') }}</title>
    @else
        <title>{{ config('app.name', 'Barbapappa') }}</title>
    @endif

    {{-- Styles --}}
    <link href="{{ asset('css/glyphicons-packed.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/flag-icon.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/semantic.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />

    {{-- Scripts --}}
    <script type="text/javascript" src="{{ asset('js/jquery-packed.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/semantic.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/lang.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>

    @stack('styles')
    @stack('scripts')

</head>
<body>
    @include('includes.sidebarMainMenu')
    @include('includes.sidebarMessages')

    <div class="pusher">
        @include('includes.toolbar')

        <div class="ui container page">
            @include('includes.impersonate')

            @include('includes.message')

            @yield('content')
        </div>
    </div>
</body>
</html>
