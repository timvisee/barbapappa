@extends('layouts.app')

@section('content')

    <div class="highlight-box">
        <h3>@lang('misc.welcomeTo')</h3>
        <img src="{{ asset('img/logo/logo_header_big.png') }}" class="logo" />
    </div>

    <ul class="ui-listview center" data-role="listview">
        <li>
            <a href="{{ route('login') }}" class="ui-btn ui-btn-icon-left ui-icon-glyphicons ui-icon-glyphicons-user">
                @lang('auth.login')
            </a>
        </li>
        <li>
            <a href="{{ route('register') }}" class="ui-btn ui-btn-icon-left ui-icon-glyphicons ui-icon-glyphicons-user-asterisk">
                @lang('auth.register')
            </a>
        </li>
    </ul>



    @for($i = 0; $i < 16; $i++)
        <br />
    @endfor

    <h1>
        <span class="glyphicons glyphicons-dashboard"></span>
        {{ $title }}</h1>
    <p>Index page.</p>

    <hr />

    <table>
        <tr>
            <td>Auth:</td>
            <td>{{ $auth }}</td>
        </tr>
        <tr>
            <td>Verified:</td>
            <td>{{ $verified }}</td>
        </tr>
    </table>

@endsection
