@extends('layouts.app')

@section('title', __('pages.paymentService.editService'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open([
        'action' => [
            'PaymentServiceController@doEdit',
            $community->human_id,
            $economy->id,
            $service->id,
        ],
        'method' => 'PUT',
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
                        {!! $service->enabled ? 'checked="checked"' : '' !!}>
                {{ Form::label('enabled', __('pages.paymentService.enabledDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('enabled') }}
        </div>

        <div class="field {{ ErrorRenderer::hasError('currency') ? 'error' : '' }}">
            {{ Form::label('currency', __('misc.currency')) }}

            <div class="ui fluid selection dropdown">
                <input type="hidden" name="currency" value="{{ $service->currency_id }}" />
                <i class="dropdown icon"></i>

                <div class="default text">@lang('misc.pleaseSpecify')</div>
                <div class="menu">
                    @foreach($currencies as $c)
                        <div class="item" data-value="{{ $c->id }}">{{ $c->displayName }}</div>
                    @endforeach
                </div>
            </div>

            {{ ErrorRenderer::inline('currency') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('deposit') ? 'error' : '' }}">
            <div class="ui toggle checkbox">
                <input type="checkbox"
                        name="deposit"
                        tabindex="0"
                        class="hidden"
                        {!! $service->deposit ? 'checked="checked"' : '' !!}>
                {{ Form::label('deposit', __('pages.paymentService.supportDepositDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('deposit') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('withdraw') ? 'error' : '' }}">
            <div class="ui toggle checkbox">
                <input type="checkbox"
                        name="withdraw"
                        tabindex="0"
                        class="hidden"
                        {!! $service->withdraw ? 'checked="checked"' : '' !!}>
                {{ Form::label('withdraw', __('pages.paymentService.supportWithdrawDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('withdraw') }}
        </div>

        <div class="ui divider"></div>

        {{-- Embed servicable specific view --}}
        @include($serviceable::view('edit'))

        <div class="ui divider"></div>

        <button class="ui button primary" type="submit">@lang('misc.saveChanges')</button>
        <a href="{{ route('community.economy.payservice.show', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'serviceId' => $service->id,
        ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
