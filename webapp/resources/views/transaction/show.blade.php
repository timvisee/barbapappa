@extends('layouts.app')

@section('title', __('pages.transactions.details'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.description')</td>
                <td>{{ $transaction->describe(true) }}</td>
            </tr>
            <tr>
                <td>@lang('misc.amount')</td>
                <td>{!! $transaction->formatCost(BALANCE_FORMAT_COLOR) !!}</td>
            </tr>
            @if($transaction->created_by != null && $transaction->created_by != barauth()->getUser()->id)
                <tr>
                    <td>@lang('misc.initiatedBy')</td>
                    <td>{{ $transaction->owner->name }}</td>
                </tr>
            @endif
            <tr>
                <td>@lang('misc.state')</td>
                <td>{{ $transaction->stateName() }}</td>
            </tr>
            <tr>
                <td>@lang('misc.firstSeen')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $transaction->created_at])</td>
            </tr>
            @if($transaction->created_at != $transaction->updated_at)
                <tr>
                    <td>@lang('misc.lastUpdated')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $transaction->updated_at])</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- TODO: some action buttons --}}
    {{-- <p> --}}
    {{--     <div class="ui buttons"> --}}
    {{--         <a href="{{ route('community.wallet.edit', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'walletId' => $wallet->id]) }}" --}}
    {{--                 class="ui button secondary"> --}}
    {{--             @lang('misc.rename') --}}
    {{--         </a> --}}
    {{--         <a href="{{ route('community.wallet.delete', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'walletId' => $wallet->id]) }}" --}}
    {{--                 class="ui button negative"> --}}
    {{--             @lang('misc.delete') --}}
    {{--         </a> --}}
    {{--     </div> --}}
    {{-- </p> --}}

    {{-- Mutation list --}}
    @include('transaction.mutation.include.list', [
        'groups' => [
            [
                'header' => trans_choice('pages.mutations.from#', $fromMutations->count()),
                'mutations' => $fromMutations,
            ],
            [
                'header' => trans_choice('pages.mutations.to#', $toMutations->count()),
                'mutations' => $toMutations,
            ],
        ],
    ])

    {{-- Transaction references --}}
    @php
        $referencedTo = $transaction->referencedTo;
        $referencedBy = $transaction->referencedBy;

        $referenceGroups = [];
        if($referencedTo != null)
            $referenceGroups[] = [
                'header' => trans_choice('pages.transactions.referencedTo#', 1),
                'transactions' => [$referencedTo],
            ];
        if($referencedBy->isNotEmpty())
            $referenceGroups[] = [
                'header' => trans_choice('pages.transactions.referencedBy#', count($referencedBy)),
                'transactions' => $referencedBy,
            ];
    @endphp
    @if(count($referenceGroups) > 0)
        @include('transaction.include.list', [
            'groups' => $referenceGroups,
        ])
    @endif

    {{-- TODO: implement go back button! --}}
    {{-- <p> --}}
    {{--     <a href="{{ route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" --}}
    {{--             class="ui button basic"> --}}
    {{--         @lang('general.goBack') --}}
    {{--     </a> --}}
    {{-- </p> --}}
@endsection
