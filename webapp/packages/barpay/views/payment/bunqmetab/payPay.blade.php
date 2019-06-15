{{-- TODO: provide $paymentable as well --}}

{!! Form::open([
    'action' => [
        'PaymentController@doPay',
        $payment->id,
    ],
    'method' => 'POST',
    'class' => 'ui form'
]) !!}

    <div class="ui divider hidden"></div>

    <a href="{{ $payment->paymentable->bunq_tab_url }}" class="ui button primary big">
        Pay
    </a>

    <div class="ui divider hidden"></div>

    <div class="ui info message">
        @lang('barpay::payment.bunqmetab.pleasePay')<br>
        <br>
        @lang('barpay::payment.bunqmetab.handledByBunq')
    </div>

    <div class="ui divider hidden"></div>

    {{-- <div class="inline field {{ ErrorRenderer::hasError('confirm_transfer') ? 'error' : '' }}"> --}}
    {{--     <div class="ui checkbox"> --}}
    {{--         {{ Form::checkbox('confirm_transfer', true, false, ['tabindex' => 0, 'class' => 'hidden']) }} --}}
    {{--         {{ Form::label('confirm_transfer', __('barpay::payment.bunqiban.confirmTransfer')) }} --}}
    {{--     </div> --}}
    {{--     <br /> --}}
    {{--     {{ ErrorRenderer::inline('confirm_transfer') }} --}}
    {{-- </div> --}}

    {{-- <div class="ui divider hidden"></div> --}}

    <button class="ui button primary" type="submit" name="submit" value="">
        @lang('misc.continue')
    </button>
    <a href="{{ route('payment.cancel', [
                'paymentId' => $payment->id,
            ]) }}"
            class="ui button negative basic">
        @lang('general.cancel')
    </a>

{!! Form::close() !!}
