{{-- TODO: provide $paymentable as well --}}

{!! Form::open([
    'action' => [
        'PaymentController@doApprove',
        $payment->id,
    ],
    'method' => 'POST',
    'class' => 'ui form'
]) !!}

    <div class="ui info top attached message visible">
        <span class="halflings halflings-info-sign"></span>
        @lang('barpay::payment.manualiban.pleaseConfirmReceivedDescription')
    </div>

    <table class="ui compact celled definition table bottom attached">
        <tbody>
            <tr>
                <td>@lang('misc.amount')</td>
                <td>{!! $payment->formatCost(BALANCE_FORMAT_COLOR) !!}</td>
            </tr>
            @if($payment->user_id)
                <tr>
                    <td>@lang('misc.fromUser')</td>
                    <td>{{ $payment->user->name }}</td>
                </tr>
            @endif
            <tr>
                <td>@lang('barpay::misc.fromIban')</td>
                <td>
                    <code class="literal copy"
                            data-copy="{{ $payment->paymentable->from_iban }}">
                        {{ format_iban($payment->paymentable->from_iban) }}
                    </code>
                </td>
            </tr>
            <tr>
                <td>@lang('barpay::misc.paymentDescription')</td>
                <td><code class="literal copy">{{ $description }}</code></td>
            </tr>
            <tr>
                <td>@lang('barpay::misc.transferredAt')</td>
                <td>
                    {{ $payment->paymentable->transferred_at->toDateString() }}
                    ({{ $payment->paymentable->transferred_at->diffForHumans() }})
                </td>
            </tr>
        </tbody>
    </table>

    <div class="ui fluid accordion">
        <div class="title">
            <i class="dropdown icon"></i>
            @lang('barpay::misc.receivingOnAccount')
        </div>
        <div class="content">
            <table class="ui compact celled definition table">
                <tbody>
                    <tr>
                        <td>@lang('barpay::misc.atAccountHolder')</td>
                        <td><code class="literal copy">{{ $payment->paymentable->to_account_holder }}</code></td>
                    </tr>
                    <tr>
                        <td>@lang('barpay::misc.atIban')</td>
                        <td><code class="literal copy" data-copy="{{ $payment->paymentable->to_iban }}">{{ format_iban($payment->paymentable->to_iban) }}</code></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="ui divider hidden"></div>

    <div class="grouped fields {{ ErrorRenderer::hasError('choice') ? 'error' : '' }}">
        {{ Form::label('choice', __('barpay::misc.makeAChoice') . ':') }}
        {{-- TODO: translate options --}}
        <div class="field">
            <div class="ui radio checkbox">
                {{ Form::radio('choice', 'approve', false, ['class' => 'hidden', 'tabindex' => 0]) }}
                {{ Form::label('choice', __('barpay::payment.manualiban.approve.approve')) }}
            </div>
        </div>
        @if($allowDelay)
            <div class="field">
                <div class="ui radio checkbox">
                    {{ Form::radio('choice', 'delay', false, ['class' => 'hidden', 'tabindex' => 0]) }}
                    {{ Form::label('choice', __('barpay::payment.manualiban.approve.delay')) }}
                </div>
            </div>
        @endif
        <div class="field">
            <div class="ui radio checkbox">
                {{ Form::radio('choice', 'reject', false, ['class' => 'hidden', 'tabindex' => 0]) }}
                {{ Form::label('choice', __('barpay::payment.manualiban.approve.reject')) }}
            </div>
        </div>
        {{ ErrorRenderer::inline('choice') }}
    </div>

    <div class="ui divider hidden"></div>

    <div class="ui warning message visible top attached">
        <span class="halflings halflings-warning-sign"></span>
        @lang('misc.cannotBeUndone')
    </div>
    <div class="ui segment bottom attached">
        <div class="inline required field {{ ErrorRenderer::hasError('confirm') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('confirm', true, false, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('confirm', __('barpay::misc.confirmChoice')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('confirm') }}
        </div>
    </div>

    <div class="ui divider hidden"></div>

    <p>
        <button class="ui button primary" type="submit" name="submit" value="">
            @lang('misc.continue')
        </button>
    </p>
    {{-- <a href="{{ route('community.economy.payservice.index', [ --}}
    {{--     'communityId' => $community->human_id, --}}
    {{--     'economyId' => $economy->id, --}}
    {{-- ]) }}" --}}
    {{--         class="ui button basic"> --}}
    {{--     @lang('general.cancel') --}}
    {{-- </a> --}}

{!! Form::close() !!}
