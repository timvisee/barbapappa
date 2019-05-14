@extends('layouts.app')

{{-- TODO: Translate! --}}
@section('title', __('pages.wallets.transferToSelf'))

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

    <div class="ui two item menu">
        <a href="{{ route('community.wallet.transfer', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'walletId' => $wallet->id
        ]) }}"
            class="item active">@lang('pages.wallets.toSelf')</a>
        <a href="{{ route(
            'community.wallet.transfer.user',
            ['communityId' => $community->human_id, 'economyId' => $economy->id, 'walletId' => $wallet->id]
        ) }}"
            class="item">@lang('pages.wallets.toUser')</a>
    </div>

    <div class="ui hidden divider"></div>

    {!! Form::open(['action' => [
        'WalletController@doTransfer',
        $community->human_id,
        $economy->id,
        $wallet->id,
    ], 'method' => 'POST', 'class' => 'ui form']) !!}

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

    <div class="highlight-box">
        <i class="glyphicons glyphicons-arrow-down arrow-icon"></i>
    </div>

    <div class="field {{ ErrorRenderer::hasError('amount') ? 'error' : '' }}">
        <label for="amount">@lang('misc.amountInCurrency', ['currency' => $currency->name]):</label>
        <div class="ui labeled input">
            <label for="amount" class="ui label">{{ $currency->symbol }}</label>
            <input type="text" placeholder="1.23" id="amount" name="amount" value="" />
        </div>
        {{ ErrorRenderer::inline('amount') }}
    </div>

    <div class="highlight-box">
        <i class="glyphicons glyphicons-arrow-down arrow-icon"></i>
    </div>

    <div class="field {{ ErrorRenderer::hasError('to_wallet') ? 'error' : '' }}">
        {{ Form::label('to_wallet', __('pages.wallets.toSelf') . ':') }}

        <div class="ui fluid selection dropdown">
            <input type="hidden" name="to_wallet">
            <i class="dropdown icon"></i>

            <div class="default text">@lang('misc.pleaseSpecify')</div>
            <div class="menu">
                @foreach($toWallets as $toWallet)
                    <div class="item" data-value="{{ $toWallet->id }}">{{ $toWallet->name }}</div>
                @endforeach
                <div class="item" data-value="new"><i>Create new wallet</i></div>
            </div>
        </div>

        {{ ErrorRenderer::inline('to_wallet') }}
    </div>

    <div class="ui hidden divider"></div>

    <p>
        <button class="ui button primary"
            type="submit">@lang('pages.wallets.transfer')</button>
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
