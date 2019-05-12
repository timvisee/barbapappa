@extends('layouts.app')

@section('title', __('pages.passwordRequestSent.title'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.passwordRequestSent.message', ['hours' => 24])</p>
@endsection
