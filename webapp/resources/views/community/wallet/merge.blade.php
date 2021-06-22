@extends('layouts.app')

@section('title', __('pages.wallets.mergeWallets'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.wallet.index', $community);
    $menusection = 'community';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>@lang('pages.wallets.mergeDescription')</p>

    {!! Form::open(['action' => ['WalletController@doMerge', $community->human_id, $economy->id], 'method' => 'POST', 'class' => 'ui form']) !!}
        @foreach($walletGroups as $group)
            <div class="ui vertical menu fluid field {{ ErrorRenderer::hasError($group['currency_id'] . '_group') ? 'error' : '' }}">
                <h5 class="ui item header">{{ $group['currency']->name }} {{ strtolower(__('pages.wallets.title')) }}</h5>
                @foreach($group['wallets'] as $wallet)
                    @php
                        $field = $group['currency_id'] . '_' . $wallet->id . '_merge';
                    @endphp
                    <div class="item inline {{ ErrorRenderer::hasError($field) ? 'error' : '' }}">
                        <div class="ui checkbox">
                            {{ Form::checkbox($field, true, false, ['tabindex' => 0, 'class' => 'hidden']) }}
                            {{ Form::label($field, $wallet->name) }}
                        </div>
                        {{ ErrorRenderer::inline($field) }}
                        {!! $wallet->formatBalance(BALANCE_FORMAT_LABEL) !!}
                    </div>
                @endforeach
            </div>
            {{ ErrorRenderer::alert($group['currency_id'] . '_group') }}
        @endforeach

        <button class="ui button primary" type="submit">@lang('misc.merge')</button>
        <a href="{{ route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>
    {!! Form::close() !!}
@endsection
