{!! Form::open([
    'action' => [
        'PaymentController@pay',
        $payment->id,
    ],
    'method' => 'PUT',
    'class' => 'ui form'
]) !!}

    <div class="ui info top attached message visible">
        <span class="halflings halflings-info-sign"></span>
        {{-- TODO: translate --}}
        @lang('Please transfer the amount to the account as noted below. You must set the given description as well, which is used to identify your payment.')
    </div>

    <div class="ui warning attached message visible">
        <span class="halflings halflings-warning-sign"></span>
        {{-- TODO: translate --}}
        @lang('You must use the exact amount, IBAN and description for the transfer as shown below, or else your payment might be lost.')
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
                <td><code class="literal copy">{{ format_iban($payment->paymentable->to_iban) }}</code></td>
            </tr>
            @if(!empty($payment->paymentable->to_bic))
                <tr>
                    {{-- TODO: translate --}}
                    <td>To @lang('barpay::misc.bic')</td>
                    <td><code class="literal copy">{{ format_bic($payment->paymentable->to_bic) }}</code></td>
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
    <div class="field">
        {{-- TODO: translate --}}
        {{ Form::label('from_iban', __('Your IBAN') . ':') }}
        {{ Form::text('from_iban', '', ['placeholder' => __('barpay::misc.ibanPlaceholder')]) }}
    </div>

    <div class="ui divider hidden"></div>

    <div class="inline field {{ ErrorRenderer::hasError('confirm_transfer') ? 'error' : '' }}">
        <div class="ui checkbox">
            <input type="checkbox"
                    name="confirm_transfer"
                    tabindex="0"
                    class="hidden">
            {{-- TODO: translate --}}
            {{ Form::label('confirm_transfer', __('I confirm I\'ve transferred the money, and the transfer description matches')) }}
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
