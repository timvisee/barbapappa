@extends('layouts.app')

@section('title', __('pages.transactions.details'))

@php
    use \App\Models\MutationProduct;
    use \App\Models\Transaction;
    use \App\Models\Wallet;
    use \BarPay\Models\Payment;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <div class="ui divider hidden"></div>

    <p class="align-center" title="@lang('misc.description')">{!!  $transaction->describe(true) !!}</p>

    {{-- State icon --}}
    <div class="ui one small statistics">
        @switch($transaction->state)
            @case(Transaction::STATE_PENDING)
                <div class="statistic yellow">
                    <div class="value">
                        <span class="halflings halflings-hourglass" title="{{ $transaction->stateName() }}"></span>
                    </div>
                    <div class="label">@lang('misc.state')</div>
                </div>
                @break

            @case(Transaction::STATE_PROCESSING)
                <div class="statistic yellow">
                    <div class="value">
                        <span class="halflings halflings-refresh" title="{{ $transaction->stateName() }}"></span>
                    </div>
                    <div class="label">@lang('misc.state')</div>
                </div>
                @break

            @case(Transaction::STATE_SUCCESS)
                <div class="statistic green">
                    <div class="value">
                        <span class="halflings halflings-ok" title="{{ $transaction->stateName() }}"></span>
                    </div>
                    <div class="label">@lang('misc.state')</div>
                </div>
                @break

            @case(Transaction::STATE_FAILED)
                <div class="statistic red">
                    <div class="value">
                        <span class="halflings halflings-alert" title="{{ $transaction->stateName() }}"></span>
                    </div>
                    <div class="label">@lang('misc.state')</div>
                </div>
                @break

            @default
                <div class="statistic">
                    <div class="value">
                        {{ $transaction->stateName() }}
                    </div>
                    <div class="label">@lang('misc.state')</div>
                </div>
        @endswitch
    </div>
    <br>
    <div class="ui one small statistics">
        <div class="statistic">
            <div class="value">
                {!! $transaction->formatCost(BALANCE_FORMAT_COLOR) !!}
            </div>
            <div class="label">@lang('misc.amount')</div>
        </div>
    </div>

    <div class="ui divider large hidden"></div>

    @if($transaction->canUndo())
        <p>
            <a href="{{ route('transaction.undo', ['transactionId' => $transaction->id]) }}"
                    class="ui button basic">
                @lang('misc.undo')
            </a>
        </p>
    @endif

    @if(!empty($realtedObjects[MutationProduct::class]))
        <div class="ui top vertical menu fluid">
            <h5 class="ui item header">
                {{ trans_choice(
                    'pages.products.#products',
                    $realtedObjects[MutationProduct::class]->pluck('quantity')->sum()
                ) }}
            </h5>
            @foreach($realtedObjects[MutationProduct::class] as $data)
                <a class="item"
                        href="{{ route('bar.product.show', [
                            'barId' => $data->bar_id,
                            'productId' => $data->product_id,
                        ]) }}">
                    @if($data->quantity != 1)
                        <span class="subtle">{{ $data->quantity }}×</span>
                    @endif
                    {{ $data->product->displayName() }}
                    <span class="subtle">
                        @ {{ $data->bar->name }}
                    </span>

                    {!! $data->mutation->formatAmount(BALANCE_FORMAT_LABEL, ['neutral' => true]) !!}
                </a>
            @endforeach
        </div>
    @endif
    @if(!empty($realtedObjects[Payment::class]))
        <div class="ui top vertical menu fluid">
            <h5 class="ui item header">
                {{ trans_choice('pages.payments.#payments', count($realtedObjects[Payment::class])) }}
            </h5>
            @foreach($realtedObjects[Payment::class] as $payment)
                <a class="item"
                        href="{{ route('payment.show', [
                            'paymentId' => $payment->id,
                        ]) }}">
                    {{ $payment->displayName() }}
                    <span class="subtle">
                        ({{ $payment->stateName() }})
                    </span>

                    {!! $payment->formatCost(BALANCE_FORMAT_LABEL) !!}

                    <span class="sub-label">
                        @include('includes.humanTimeDiff', ['time' => $payment->updated_at ?? $payment->created_at])
                    </span>
                </a>
            @endforeach
        </div>
    @endif
    @if(!empty($realtedObjects[Wallet::class]))
        <div class="ui top vertical menu fluid">
            <h5 class="ui item header">
                {{ trans_choice('pages.wallets.#wallets', count($realtedObjects[Wallet::class])) }}
            </h5>
            @foreach($realtedObjects[Wallet::class] as $wallet)
                <a href="{{ $wallet->getUrlShow() }}"
                        class="item">
                    {{ $wallet->name }}
                </a>
            @endforeach
        </div>
    @endif

    <div class="ui divider hidden"></div>

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

    <div class="ui fluid accordion">
        <div class="title">
            <i class="dropdown icon"></i>
            @lang('misc.details')
        </div>
        <div class="content">
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
                        <td>@lang('misc.owner')</td>
                        <td>
                            @if($transaction->owner != null)
                                {{ $transaction->owner->name }}
                            @else
                                <i>@lang('misc.unknownUser')</i>
                            @endif
                        </td>
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
                    <tr>
                        <td>@lang('misc.reference')</td>
                        <td><code class="literal">transaction#{{ $transaction->id }}</code></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- TODO: implement go back button! --}}
    {{-- <p> --}}
    {{--     <a href="{{ route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" --}}
    {{--             class="ui button basic"> --}}
    {{--         @lang('general.goBack') --}}
    {{--     </a> --}}
    {{-- </p> --}}
@endsection
