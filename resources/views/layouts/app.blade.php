<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'BARbapAPPa') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/jquery-mobile.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/glyphicons-packed.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/flag-icon.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/semantic.min.css') }}" rel="stylesheet" />

    <!-- Scripts -->
    <script type="text/javascript" src="{{ asset('js/jquery-packed.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/semantic.min.js') }}"></script>
</head>
<body>
    <div data-role="page">
        @include('includes.toolbar')

        <div data-role="main" class="ui-content">
            @include('includes.message')

            @yield('content')
        </div>

        @include('includes.sidebar')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
