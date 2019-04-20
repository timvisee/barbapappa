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
                    <td>{{ $transaction->createdBy->name }}</td>
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

    <div class="ui top vertical menu fluid">
        <h5 class="ui item header">
            {{ trans_choice('pages.mutations.number#', count($mutations)) }}
        </h5>
        @forelse($mutations as $mutation)
            <a class="item"
                    href="{{ route('transaction.mutation.show', [
                        'transactionId' => $transaction->id,
                        'mutationId' => $mutation->id,
                    ]) }}">
                {{ $mutation->describe() }}
                {!! $mutation->formatAmount(BALANCE_FORMAT_LABEL); !!}
            </a>
        @endforeach
    </div>

    {{-- Transaction references --}}
    @php
        $referencedTo = $transaction->referencedTo;
        $referencedBy = $transaction->referencedBy;
    @endphp
    @if($referencedTo != null || $referencedBy->isNotEmpty())
        <div class="ui top vertical menu fluid">
            @if($referencedTo != null)
                <h5 class="ui item header">
                    @lang('misc.referencedTo') (1)
                </h5>
                <a class="item"
                        href="{{ route('transaction.show', [
                            'transactionId' => $referencedTo->id,
                        ]) }}">
                    {{ $referencedTo->describe() }}
                    {!! $referencedTo->formatCost(BALANCE_FORMAT_LABEL); !!}
                </a>
            @endif
            @if($referencedBy->isNotEmpty())
                <h5 class="ui item header">
                    @lang('misc.referencedBy') ({{ count($referencedBy) }})
                </h5>
                @forelse($referencedBy as $ref)
                    <a class="item"
                            href="{{ route('transaction.show', [
                                'transactionId' => $ref->id,
                            ]) }}">
                        {{ $ref->describe() }}
                        {!! $ref->formatCost(BALANCE_FORMAT_LABEL); !!}
                    </a>
                @endforeach
            @endif
        </div>
    @endif

    <br />

    {{-- TODO: implement go back button! --}}
    {{-- <p> --}}
    {{--     <a href="{{ route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" --}}
    {{--             class="ui button basic"> --}}
    {{--         @lang('general.goBack') --}}
    {{--     </a> --}}
    {{-- </p> --}}
@endsection
