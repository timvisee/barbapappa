@extends('layouts.app')

@section('title', __('pages.wallets.topUp'))

@section('content')
    <h2 class="ui header">
        @yield('title')

        {{-- <div class="sub header"> --}}
        {{--     @lang('misc.in') --}}
        {{--     <a href="{{ route('community.wallet.index', ['communityId' => $community->human_id]) }}"> --}}
        {{--         {{ $community->name }} --}}
        {{--     </a> --}}
        {{--     @lang('misc.for') --}}
        {{--     <a href="{{ route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"> --}}
        {{--         {{ $economy->name }} --}}
        {{--     </a> --}}
        {{-- </div> --}}
    </h2>

    {!! Form::open(['action' => [
        'WalletController@doTopUp',
        $community->human_id,
        $economy->id,
        $wallet->id,
    ], 'method' => 'POST', 'class' => 'ui form']) !!}

    <div class="ui two item menu">
        <a href="{{ route('community.wallet.topUp', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'walletId' => $wallet->id
        ]) }}"
            class="item active">@lang('misc.deposit')</a>
        <a href="#" class="item disabled">@lang('misc.withdraw')</a>
    </div>

    <div class="ui hidden divider"></div>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.name')</td>
                <td>{{ $wallet->name }}</td>
            </tr>
            <tr>
                <td>@lang('misc.balance')</td>
                <td>{!! $wallet->formatBalance(BALANCE_FORMAT_COLOR) !!}</td>
            </tr>
        </tbody>
    </table>

    <div class="field {{ ErrorRenderer::hasError('amount') ? 'error' : '' }}">
        <label for="amount">@lang('pages.paymentService.amountToTopUpInCurrency', ['currency' => $currency->name]):</label>
        <div class="ui labeled input">
            <label for="amount" class="ui label">{{ $currency->symbol }}</label>
            <input type="text" placeholder="1.23" id="amount" name="amount" value="" />
        </div>
        {{ ErrorRenderer::inline('amount') }}
    </div>

    <div class="grouped fields">
        {{ Form::label('payment_service', __('pages.paymentService.selectPaymentServiceToUse')) }}
        @foreach($services as $service)
            <div class="field">
                <div class="ui radio checkbox">
                    {{ Form::radio('payment_service', $service->id, ['class' => 'hidden', 'tabindex' => 0]) }}
                    {{ Form::label('payment_service', $service->displayName() .  ' (' . $service->serviceable->__('duration') . ')') }}
                </div>
            </div>
        @endforeach
    </div>

    <div class="ui hidden divider"></div>

    <p>
        <button class="ui button primary"
            type="submit">@lang('pages.wallets.topUp')</button>
        <a href="{{ route('community.wallet.show', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'walletId' => $wallet->id,
                ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>
    </p>

    {!! Form::close() !!}
@endsection
