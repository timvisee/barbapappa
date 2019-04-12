@extends('layouts.app')

@section('title', __('misc.welcomeTo'))

@section('content')
    <div class="highlight-box">
        <h2 class="ui header">@yield('title')</h2>
        {{ logo()->element(true, ['class' => 'logo']) }}
    </div>

    <div class="ui stackable two column grid">
        <div class="column">
            <a href="{{ route('login') }}" class="ui button fluid large">@lang('auth.login')</a>
        </div>
        <div class="column">
            <a href="{{ route('register') }}" class="ui button fluid large">@lang('auth.register')</a>
        </div>
    </div>
@endsection
