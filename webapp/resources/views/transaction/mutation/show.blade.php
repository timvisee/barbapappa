@extends('layouts.app')

@php
    use \App\Models\MutationWallet;
@endphp

@section('title', __('pages.mutations.details'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.description')</td>
                <td>{!! $mutation->describe($detail = true) !!}</td>
            </tr>
            <tr>
                <td>@lang('misc.amount')</td>
                <td>{!! $mutation->formatAmount(BALANCE_FORMAT_COLOR) !!}</td>
            </tr>
            {{-- TODO: show proper state, and translate --}}
            <tr>
                <td>State</td>
                <td>{{ $mutation->stateName() }}</td>
            </tr>
            <tr>
                <td>@lang('misc.createdAt')</td>
                <td>{{ $mutation->created_at }}</td>
            </tr>
            @if($mutation->created_at != $mutation->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>{{ $mutation->updated_at }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- Metadata for transaction types --}}
    @if($mutation->hasMutationData())
        @php
            // TODO: eager load data.wallet.economy here to improve performance!
            $data = $mutation->mutationData()->firstOrFail();
        @endphp
        @if($data instanceof MutationWallet)
            <p>
                {{-- TODO: inefficient querying here, improve this! --}}
                <a href="{{ route('community.wallet.show', [
                    'communityId' => $data->wallet->economy->community_id,
                    'economyId' => $data->wallet->economy_id,
                    'walletId' => $data->wallet_id,
                ]) }}"
                        class="ui button basic">
                    @lang('pages.wallets.view')
                </a>
            </p>
        @else
            <p>
                TODO: show mutation type specific data, not yet implemented
            </p>
        @endif
    @endif

    {{-- Dependencies and dependents --}}
    @php
        $dependOn = $mutation->dependOn;
        $dependents = $mutation->dependents;
    @endphp
    @if($dependOn != null || $dependents->isNotEmpty())
        <div class="ui top vertical menu fluid">
            @if($dependOn != null)
                <h5 class="ui item header">
                    @lang('misc.dependsOn') (1)
                </h5>
                <a class="item"
                        href="{{ route('transaction.mutation.show', [
                            'transactionId' => $transaction->id,
                            'mutationId' => $dependOn->id,
                        ]) }}">
                    {{ $dependOn->describe() }}
                    {!! $dependOn->formatAmount(BALANCE_FORMAT_LABEL); !!}
                </a>
            @endif
            @if($dependents->isNotEmpty())
                <h5 class="ui item header">
                    @lang('misc.dependents') ({{ count($dependents) }})
                </h5>
                @forelse($dependents as $dependent)
                    <a class="item"
                            href="{{ route('transaction.mutation.show', [
                                'transactionId' => $transaction->id,
                                'mutationId' => $dependent->id,
                            ]) }}">
                        {{ $dependent->describe() }}
                        {!! $dependent->formatAmount(BALANCE_FORMAT_LABEL); !!}
                    </a>
                @endforeach
            @endif
        </div>
        <br />
    @endif

    {{-- <div class="ui top vertical menu fluid"> --}}
    {{--     <h5 class="ui item header"> --}}
    {{--         {{ trans_choice('pages.mutations.number#', count($mutations)) }} --}}
    {{--     </h5> --}}
    {{--     @forelse($mutations as $mutation) --}}
    {{--         <a class="item" --}}
    {{--                 href="{{ route('transaction.mutation.show', [ --}}
    {{--                     'transactionId' => $transaction->id, --}}
    {{--                     'mutationId' => $mutation->id, --}}
    {{--                 ]) }}"> --}}
    {{--             {{ $mutation->describe() }} --}}
    {{--             {!! $mutation->formatAmount(BALANCE_FORMAT_LABEL); !!} --}}
    {{--         </a> --}}
    {{--     @endforeach --}}
    {{-- </div> --}}
    {{-- <br /> --}}

    <p>
        <a href="{{ route('transaction.show', ['transactionId' => $transaction->id]) }}"
                class="ui button basic">
            @lang('pages.transactions.backToTransaction')
        </a>
    </p>
@endsection
