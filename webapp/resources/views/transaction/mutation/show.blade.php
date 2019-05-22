@extends('layouts.app')

@php
    use \App\Models\MutationWallet;
    use \App\Models\MutationProduct;
    use \App\Models\MutationPayment;

    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.transactions.backToTransaction'),
        'link' => route('transaction.show', ['transactionId' => $transaction->id]),
        'icon' => 'undo',
    ];
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
                <td>
                    {!! $mutation->formatAmount(BALANCE_FORMAT_COLOR, true) !!}
                    @if($mutation->amount >= 0)
                        @lang('pages.transactions.toTransaction')
                    @else
                        @lang('pages.transactions.fromTransaction')
                    @endif
                </td>
            </tr>
            @if($transaction->owner_id != null && $transaction->owner_id != barauth()->getUser()->id)
                <tr>
                    <td>@lang('misc.initiatedBy')</td>
                    <td>{{ $transaction->owner->name }}</td>
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
            @php
                // Extend page links
                $menulinks[] = [
                    'name' => __('pages.wallets.view'),
                    'link' => $data->wallet->getUrlShow(),
                    'icon' => 'wallet',
                ];
            @endphp

            <p>
                {{-- TODO: inefficient querying here, improve this! --}}
                <a href="{{ $data->wallet->getUrlShow() }}"
                        class="ui button basic">
                    @lang('pages.wallets.view')
                </a>
            </p>
        @elseif($data instanceof MutationProduct)
            @php
                // Extend page links
                $menulinks[] = [
                    'name' => __('pages.products.viewProduct'),
                    'link' => route('bar.product.show', [
                            'barId' => $mutation->mutationData->bar->human_id,
                            'productId' => $mutation->mutationData->product_id,
                        ]),
                    'icon' => 'shopping-bag',
                ];
                $menulinks[] = [
                    'name' => __('pages.bar.viewBar'),
                    'link' => route('bar.show', [
                            'barId' => $mutation->mutationData->bar->human_id,
                        ]),
                    'icon' => 'beer',
                ];
            @endphp

            <div class="ui top vertical menu fluid">
                <h5 class="ui item header">Product</h5>

                @php
                    // Get the product
                    $product = $mutation
                        ->mutationData
                        ->product()
                        ->withTrashed()
                        ->first();
                    $trashed = $product == null || $product->trashed();
                @endphp

                <a class="item disabled"
                        href="{{ !$trashed ? route('bar.product.show', [
                            'barId' => $mutation->mutationData->bar->human_id,
                            'productId' => $mutation->mutationData->product_id,
                        ]) : '#'}}">
                    @if($data->quantity != 1)
                        <span class="subtle">{{ $data->quantity }}×</span>
                    @endif
                    {{ $product != null ? $product->displayName() : __('pages.products.unknownProduct') }}
                    <span class="subtle">
                        @ {{ $data->bar->name }}
                    </span>

                    {!! $mutation->formatAmount(BALANCE_FORMAT_LABEL, true) !!}
                </a>
            </div>
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
                'header' => trans_choice('pages.mutations.dependsOn#', 1),
                'mutations' => [$dependOn],
            ];
        if($dependents->isNotEmpty())
            $dependGroups[] = [
                'header' => trans_choice('pages.mutations.dependentBy#', count($dependents)),
                'mutations' => $dependents,
            ];
    @endphp
    @if(count($dependGroups) > 0)
        @include('transaction.mutation.include.list', [
            'groups' => $dependGroups,
        ])
    @endif

    <a href="{{ route('transaction.show', ['transactionId' => $transaction->id]) }}"
            class="ui button basic">
        @lang('pages.transactions.backToTransaction')
    </a>
@endsection
