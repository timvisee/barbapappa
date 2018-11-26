@extends('layouts.app')

@section('content')

    <div class="highlight-box">
        <h2 class="ui header">@lang('misc.welcomeTo')</h2>
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
