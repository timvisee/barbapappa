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
        Pay via bunq
    </a>

    <div class="ui divider hidden"></div>

    @php
        $bunqAccount = $payment->paymentable->getBunqAccount();
    @endphp

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.amount')</td>
            <td>{!! $payment->formatCost(BALANCE_FORMAT_COLOR) !!}</td>
            </tr>
            {{-- <tr> --}}
            {{--     <td>@lang('barpay::misc.toAccountHolder')</td> --}}
            {{--     <td><code class="literal copy">{{ $bunqAccount->account_holder }}</code></td> --}}
            {{-- </tr> --}}
            {{-- <tr> --}}
            {{--     <td>@lang('barpay::misc.toIban')</td> --}}
            {{--     <td><code class="literal copy" data-copy="{{ $bunqAccount->iban }}">{{ format_iban($bunqAccount->iban) }}</code></td> --}}
            {{-- </tr> --}}
            {{-- @if(!empty($bunqAccount->bic)) --}}
            {{--     <tr> --}}
            {{--         <td>@lang('barpay::misc.toBic')</td> --}}
            {{--         <td><code class="literal copy" data-copy="{{ $bunqAccount->bic }}">{{ format_bic($bunqAccount->bic) }}</code></td> --}}
            {{--     </tr> --}}
            {{-- @endif --}}
            {{-- <tr> --}}
            {{--     <td>@lang('barpay::misc.paymentDescription')</td> --}}
            {{--     <td><code class="literal copy">{{ $description }}</code></td> --}}
            {{-- </tr> --}}
        </tbody>
    </table>

    <div class="ui divider hidden"></div>

    <div class="inline field {{ ErrorRenderer::hasError('confirm_transfer') ? 'error' : '' }}">
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
