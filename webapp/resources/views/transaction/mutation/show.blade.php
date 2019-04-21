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
            @if($transaction->created_by != null && $transaction->created_by != barauth()->getUser()->id)
                <tr>
                    <td>@lang('misc.initiatedBy')</td>
                    <td>{{ $transaction->createdBy->name }}</td>
                </tr>
            @endif
            <tr>
                <td>@lang('misc.state')</td>
                <td>{{ $mutation->stateName() }}</td>
            </tr>
            <tr>
                <td>@lang('misc.firstSeen')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $mutation->created_at])</td>
            </tr>
            @if($mutation->created_at != $mutation->updated_at)
                <tr>
                    <td>@lang('misc.lastUpdated')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $mutation->updated_at])</td>
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
                <a href="{{ $data->wallet->getUrlShow() }}"
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

        $dependGroups = [];
        if($dependOn != null)
            $dependGroups[] = [
                'header' => __('misc.dependsOn') . ' (1)',
                'mutations' => [$dependOn],
            ];
        if($dependents->isNotEmpty())
            $dependGroups[] = [
                'header' => __('misc.dependents') . ' (' . count($dependents) . ')',
                'mutations' => $dependents,
            ];
    @endphp
    @if(count($dependGroups) > 0)
        @include('transaction.mutation.include.list', [
            'groups' => $dependGroups,
        ])
    @endif

    <p>
        <a href="{{ route('transaction.show', ['transactionId' => $transaction->id]) }}"
                class="ui button basic">
            @lang('pages.transactions.backToTransaction')
        </a>
    </p>

    <br />
@endsection
