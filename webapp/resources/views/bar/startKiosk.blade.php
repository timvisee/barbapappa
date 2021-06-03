@extends('layouts.app')

@section('title', __('pages.bar.startKiosk'))

@section('content')
    <h2 class="ui header">
        @yield('title')

        <div class="sub header">
            @lang('misc.for')
            <a href="{{ route('bar.manage', ['barId' => $bar->human_id]) }}">
                {{ $bar->name }}
            </a>
        </div>
    </h2>
    <p>@lang('pages.bar.startKioskDescription', ['app' => config('app.name')])</p>

    {!! Form::open(['action' => ['BarController@doStartKiosk', 'barId' => $bar->human_id], 'method' => 'POST', 'class' => 'ui form']) !!}

        <div class="ui divider hidden"></div>

        <div class="ui warning top attached message visible">
            <span class="halflings halflings-warning-sign"></span>
            @lang('pages.bar.startKioskConfirmDescription')
        </div>

        <div class="ui segment bottom attached">
            <div class="inline required field {{ ErrorRenderer::hasError('confirm') ? 'error' : '' }}">
                <div class="ui checkbox">
                    {{ Form::checkbox('confirm', true, false, ['tabindex' => 0, 'class' => 'hidden']) }}
                    {{ Form::label('confirm', __('pages.bar.startKioskConfirm')) }}
                </div>
                <br />
                {{ ErrorRenderer::inline('confirm') }}
            </div>
        </div>

        <div class="ui divider hidden"></div>

        <button class="ui button primary" type="submit">@lang('misc.start')</button>
        <a href="{{ route('bar.manage', ['barId' => $bar->human_id]) }}"
                class="ui button basic">
            @lang('pages.bar.backToBar')
        </a>

    {!! Form::close() !!}
@endsection
