@extends('layouts.app')

@section('content')
    <h1>@lang('lang.language', [], 'en') / @lang('lang.language', [], 'nl')</h1>
    <p>@lang('lang.choose', [], 'en') / @lang('lang.choose', [], 'nl'):</p>
    <ul>
        <li><a href="{{ route('language', ['locale' => 'en']) }}">@lang('lang.name', [], 'en')</a></li>
        <li><a href="{{ route('language', ['locale' => 'nl']) }}">@lang('lang.name', [], 'nl')</a></li>
        <li><a href="{{ route('language', ['locale' => 'pirate']) }}">@lang('lang.name', [], 'pirate')</a></li>
    </ul>

@endsection
