{{-- TODO: provide $paymentable as well --}}

{!! Form::open([
    'action' => [
        'PaymentController@doPay',
        $payment->id,
    ],
    'method' => 'POST',
    'class' => 'ui form'
]) !!}

    <div class="ui info top attached message visible">
        <span class="halflings halflings-info-sign"></span>
        @lang('barpay::payment.bunqiban.pleaseTransferSameDescription')
    </div>

    <table class="ui compact celled definition table bottom attached">
        <tbody>
            <tr>
                <td>@lang('misc.amount')</td>
                <td>{!! $payment->formatCost(BALANCE_FORMAT_COLOR) !!}</td>
            </tr>
            <tr>
                <td>@lang('barpay::misc.toAccountHolder')</td>
                <td><code class="literal copy">{{ $bunqAccount->account_holder }}</code></td>
            </tr>
            <tr>
                <td>@lang('barpay::misc.toIban')</td>
                <td><code class="literal copy" data-copy="{{ $bunqAccount->iban }}">{{ format_iban($bunqAccount->iban) }}</code></td>
            </tr>
            @if(!empty($bunqAccount->bic))
                <tr>
                    <td>@lang('barpay::misc.toBic')</td>
                    <td><code class="literal copy" data-copy="{{ $bunqAccount->bic }}">{{ format_bic($bunqAccount->bic) }}</code></td>
                </tr>
            @endif
            <tr>
                <td>@lang('barpay::misc.paymentDescription')</td>
                <td><code class="literal copy">{{ $description }}</code></td>
            </tr>
        </tbody>
    </table>

    {{-- SEPA payment QR code --}}
    @if(isset($paymentQrPayload))
        <div class="ui fluid accordion">
            <div class="title">
                <i class="dropdown icon"></i>
                @lang('barpay::payment.qr.showPaymentQr')
            </div>
            <div class="content">
                <p class="transition hidden">
                    @lang('barpay::payment.qr.instruction')
                    <a href="https://www.scan2pay.info/#av_section_8"
                            target="_blank">
                        @lang('misc.moreInfo')
                    </a>
                </p>
                <img src="data:image/png;base64,{{ base64_encode(
                    QrCode::format('png')
                        ->size(300)
                        ->margin(1)
                        ->errorCorrection('M')
                        ->generate($paymentQrPayload)
                    ) }}">
            </div>
        </div>
    @endif

    <div class="ui divider hidden"></div>

    <p>@lang('barpay::payment.bunqiban.enterOwnIban')</p>
    <div class="required field {{ ErrorRenderer::hasError('iban') ? 'error' : '' }}">
        {{ Form::label('iban', __('barpay::misc.yourIban') . ':') }}
        {{ Form::text('iban', '', ['placeholder' => __('barpay::misc.ibanPlaceholder')]) }}
        {{ ErrorRenderer::inline('iban') }}
    </div>

    <div class="ui divider hidden"></div>

    <div class="inline required field {{ ErrorRenderer::hasError('confirm_transfer') ? 'error' : '' }}">
        <div class="ui checkbox">
            {{ Form::checkbox('confirm_transfer', true, false, ['tabindex' => 0, 'class' => 'hidden']) }}
            {{ Form::label('confirm_transfer', __('barpay::payment.bunqiban.confirmTransfer')) }}
        </div>
        <br />
        {{ ErrorRenderer::inline('confirm_transfer') }}
    </div>

    <div class="ui divider hidden"></div>

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
