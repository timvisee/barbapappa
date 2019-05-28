@extends('layouts.app')

@section('title', __('pages.paymentService.newService'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open([
        'action' => [
            'PaymentServiceController@doCreate',
            $community->human_id,
            $economy->id,
        ],
        'method' => 'POST',
        'class' => 'ui form'
    ]) !!}
        <div class="field disabled">
            {{ Form::label('type', __('pages.paymentService.serviceType') . ':') }}
            {{ Form::text('type', $serviceable::name()) }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('enabled') ? 'error' : '' }}">
            <div class="ui checkbox">
                <input type="checkbox"
                        name="enabled"
                        tabindex="0"
                        class="hidden"
                        checked="checked">
                {{ Form::label('enabled', __('pages.paymentService.enabledDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('enabled') }}
        </div>

        <br />

        <button class="ui button primary" type="submit" name="submit" value="">
            @lang('misc.add')
        </button>
        <a href="{{ route('community.economy.payservice.index', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
        ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
