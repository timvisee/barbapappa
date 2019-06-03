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
        {{-- TODO: translate --}}
        @lang('Please transfer the amount to the account as noted below. You must use the exact same description which is used to identify your payment, or your payment might be lost.')
    </div>

    <table class="ui compact celled definition table bottom attached">
        <tbody>
            <tr>
                <td>@lang('misc.amount')</td>
            <td>{!! $payment->formatCost(BALANCE_FORMAT_COLOR) !!}</td>
            </tr>
            <tr>
                {{-- TODO: translate --}}
                <td>To @lang('Account holder')</td>
                <td><code class="literal copy">{{ $payment->paymentable->to_account_holder }}</code></td>
            </tr>
            <tr>
                {{-- TODO: translate --}}
                <td>To @lang('barpay::misc.iban')</td>
                <td><code class="literal copy" data-copy="{{ $payment->paymentable->to_iban }}">{{ format_iban($payment->paymentable->to_iban) }}</code></td>
            </tr>
            @if(!empty($payment->paymentable->to_bic))
                <tr>
                    {{-- TODO: translate --}}
                    <td>To @lang('barpay::misc.bic')</td>
                    <td><code class="literal copy" data-copy="{{ $payment->paymentable->to_bic }}">{{ format_bic($payment->paymentable->to_bic) }}</code></td>
                </tr>
            @endif
            <tr>
                {{-- TODO: translate --}}
                <td>@lang('Payment description')</td>
                <td><code class="literal copy">BarApp {{ format_payment_reference($payment->reference) }}</code></td>
            </tr>
        </tbody>
    </table>

    <div class="ui divider hidden"></div>

    <p>
        {{-- TODO: translate --}}
        Enter the IBAN you're transferring the money from, so we can link the
        payment to your account.
    </p>
    <div class="field {{ ErrorRenderer::hasError('iban') ? 'error' : '' }}">
        {{-- TODO: translate --}}
        {{ Form::label('iban', __('Your IBAN') . ':') }}
        {{ Form::text('iban', '', ['placeholder' => __('barpay::misc.ibanPlaceholder')]) }}
        {{ ErrorRenderer::inline('iban') }}
    </div>

    <div class="ui divider hidden"></div>

    <div class="inline field {{ ErrorRenderer::hasError('confirm_transfer') ? 'error' : '' }}">
        <div class="ui checkbox">
            <input type="checkbox"
                    name="confirm_transfer"
                    tabindex="0"
                    class="hidden">
            {{-- TODO: translate --}}
            {{ Form::label('confirm_transfer', __('I confirm I\'ve transferred the money with the given payment details')) }}
        </div>
        <br />
        {{ ErrorRenderer::inline('confirm_transfer') }}
    </div>

    <div class="ui divider hidden"></div>

    <button class="ui button primary" type="submit" name="submit" value="">
        @lang('misc.continue')
    </button>
    {{-- <a href="{{ route('community.economy.payservice.index', [ --}}
    {{--     'communityId' => $community->human_id, --}}
    {{--     'economyId' => $economy->id, --}}
    {{-- ]) }}" --}}
    {{--         class="ui button basic"> --}}
    {{--     @lang('general.cancel') --}}
    {{-- </a> --}}

{!! Form::close() !!}
