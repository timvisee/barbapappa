@extends('layouts.app')

@section('title', __('pages.payments.cancelPayment'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.payments.cancelPaymentQuestion')</p>

    <table class="ui compact celled definition table">
        <tbody>
            @if($payment->user_id == barauth()->getUser()->id)
                <tr>
                    <td>@lang('misc.user')</td>
                    <td>{{ $payment->user->name }}</td>
                </tr>
            @endif
            <tr>
                <td>@lang('misc.amount')</td>
                <td>{!! $payment->formatCost(BALANCE_FORMAT_COLOR) !!}</td>
            </tr>
            <tr>
                <td>@lang('misc.initiatedAt')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $payment->created_at])</td>
            </tr>
            <tr>
                <td>@lang('pages.paymentService.serviceType')</td>
                <td>{{ $payment->service->displayName() }}</td>
            </tr>
        </tbody>
    </table>

    <div class="ui warning top attached message visible">
        <span class="halflings halflings-warning-sign"></span>
        @lang('misc.cannotBeUndone')
    </div>

    {!! Form::open(['action' => [
        'PaymentController@doCancel',
        'paymentId' => $payment->id
    ], 'method' => 'DELETE', 'class' => 'ui form']) !!}
        {{-- Delete confirmation checkbox --}}
        <div class="ui bottom attached segment">
            <div class="required field {{ ErrorRenderer::hasError('confirm') ? 'error' : '' }}">
                <div class="ui checkbox">
                    {{ Form::checkbox('confirm', 1, false, [
                        'tabindex' => 0,
                        'class' => 'hidden',
                    ]) }}
                    {{ Form::label('confirm', __('misc.confirmCancel')) }}
                </div>
                <br />
                {{ ErrorRenderer::inline('confirm') }}
            </div>
        </div>
        <br />

        <div class="ui buttons">
            <a href="{{ route('payment.show', ['paymentId' => $payment->id]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesCancel')</button>
        </div>
    {!! Form::close() !!}
@endsection
