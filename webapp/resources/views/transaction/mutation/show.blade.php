@extends('layouts.app')

@section('title', __('pages.mutations.details'))
@php
    $breadcrumbs = Breadcrumbs::generate('transaction.mutation.show', $mutation);
@endphp

@php
    use \App\Models\Mutation;
    use \App\Models\MutationBalanceImport;
    use \App\Models\MutationMagic;
    use \App\Models\MutationPayment;
    use \App\Models\MutationProduct;
    use \App\Models\MutationWallet;

    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.transactions.backToTransaction'),
        'link' => route('transaction.show', ['transactionId' => $transaction->id]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <div class="ui divider hidden"></div>

    <p class="align-center" title="@lang('misc.description')">{!!  $mutation->describe($detail = true) !!}</p>

    {{-- Amount & state icon --}}
    <div class="ui one small statistics">
        <div class="statistic">
            <div class="value">
                {!! $mutation->formatAmount(BALANCE_FORMAT_COLOR, ['neutral' => true]) !!}
            </div>
            <div class="label">
                @if($mutation->amount >= 0)
                    @lang('pages.transactions.toTransaction')
                @else
                    @lang('pages.transactions.fromTransaction')
                @endif
            </div>
        </div>
    </div>
    <br>
    <div class="ui one small statistics">
        @switch($mutation->state)
            @case(Mutation::STATE_PENDING)
                <div class="statistic yellow">
                    <div class="value">
                        <span class="halflings halflings-hourglass" title="{{ $mutation->stateName() }}"></span>
                    </div>
                    <div class="label">@lang('misc.state')</div>
                </div>
                @break

            @case(Mutation::STATE_PROCESSING)
                <div class="statistic yellow">
                    <div class="value">
                        <span class="halflings halflings-refresh" title="{{ $mutation->stateName() }}"></span>
                    </div>
                    <div class="label">@lang('misc.state')</div>
                </div>
                @break

            @case(Mutation::STATE_SUCCESS)
                <div class="statistic green">
                    <div class="value">
                        <span class="halflings halflings-ok" title="{{ $mutation->stateName() }}"></span>
                    </div>
                    <div class="label">@lang('misc.state')</div>
                </div>
                @break

            @case(Mutation::STATE_FAILED)
                <div class="statistic red">
                    <div class="value">
                        <span class="halflings halflings-alert" title="{{ $mutation->stateName() }}"></span>
                    </div>
                    <div class="label">@lang('misc.state')</div>
                </div>
                @break

            @default
                <div class="statistic">
                    <div class="value">
                        {{ $mutation->stateName() }}
                    </div>
                    <div class="label">@lang('misc.state')</div>
                </div>
        @endswitch
    </div>

    <div class="ui divider hidden"></div>

    <p>
        <a href="{{ route('transaction.show', ['transactionId' => $transaction->id]) }}"
                class="ui button basic">
            @lang('pages.transactions.backToTransaction')
        </a>
    </p>

    <div class="ui fluid accordion">
        <div class="title">
            <i class="dropdown icon"></i>
            @lang('misc.details')
        </div>
        <div class="content">
            {{-- Metadata for transaction types --}}
            @php
                // TODO: eager load data.wallet.economy here to improve performance!
                $data = $mutation->mutationable;
            @endphp
            @if($data != null)
                @if($data instanceof MutationMagic)
                    <table class="ui compact celled definition table">
                        <tbody>
                            <tr>
                                <td>@lang('misc.description')</td>
                                <td>
                                    @if($data->description)
                                        {{ $data->description }}
                                    @else
                                        <i>@lang('misc.none')</i>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                @elseif($data instanceof MutationWallet && $data->wallet_id != null)
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
                                    'barId' => $data->bar->human_id,
                                    'productId' => $data->product_id,
                                ]),
                            'icon' => 'shopping-bag',
                        ];
                        $menulinks[] = [
                            'name' => __('pages.bar.viewBar'),
                            'link' => route('bar.show', [
                                    'barId' => $data->bar->human_id,
                                ]),
                            'icon' => 'beer',
                        ];
                    @endphp

                    <div class="ui top vertical menu fluid">
                        <h5 class="ui item header">@lang('misc.product')</h5>

                        @php
                            // Get the product
                            $product = $data->product()->withTrashed()->first();
                            $trashed = $product == null || $product->trashed();
                        @endphp

                        <a class="item"
                                href="{{ !$trashed ? route('bar.product.show', [
                                    'barId' => $data->bar->human_id,
                                    'productId' => $data->product_id,
                                ]) : '#'}}">
                            @if($data->quantity != 1)
                                <span class="subtle">{{ $data->quantity }}×</span>
                            @endif
                            {{ $product != null ? $product->displayName() : __('pages.products.unknownProduct') }}
                            <span class="subtle">
                                @ {{ $data->bar->name }}
                            </span>

                            {!! $mutation->formatAmount(BALANCE_FORMAT_LABEL, ['neutral' => true]) !!}
                        </a>
                    </div>
                @elseif($data instanceof MutationPayment)
                    @php
                        // Extend page links
                        $menulinks[] = [
                            'name' => __('pages.payments.viewPayment'),
                            'link' => route('payment.show', [
                                'paymentId' => $data->payment_id,
                            ]),
                            'icon' => 'credit-card',
                        ];
                    @endphp

                    <div class="ui top vertical menu fluid">
                        <h5 class="ui item header">@lang('misc.payment')</h5>

                        @php
                            // Get the payment
                            $payment = $data->payment;
                        @endphp

                        <a class="item"
                                href="{{ $payment != null ? route('payment.show', [
                                    'paymentId' => $payment->id,
                                ]) : '#'}}">
                            {{ $payment != null ? $payment->displayName() : __('pages.payments.unknownPayment') }}
                            <span class="subtle">
                                ({{ $payment->stateName() }})
                            </span>

                            {!! $mutation->formatAmount(BALANCE_FORMAT_LABEL, ['neutral' => true]) !!}

                            <span class="sub-label">
                                @include('includes.humanTimeDiff', ['time' => $payment->updated_at ?? $payment->created_at])
                            </span>
                        </a>
                    </div>
                @elseif($data instanceof MutationBalanceImport)
                    @php
                        $change = $data->balanceImportChange;
                        $submitter = $change != null ? $change->submitter : null;
                        $system = $change != null ? $change->event->system : null;
                    @endphp

                    <table class="ui compact celled definition table">
                        <tbody>
                            <tr>
                                <td>@lang('misc.initiatedAt')</td>
                                <td>
                                    @if($change != null)
                                        {{ $change->created_at->toDateString() }}
                                        ({{ $change->created_at->diffForHumans() }})
                                    @else
                                        <i>@lang('misc.unknown')</i>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>@lang('misc.initiatedBy')</td>
                                <td>
                                    @if($submitter != null)
                                        {{ $submitter->name }}
                                    @else
                                        <i>@lang('misc.unknownUser')</i>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>@lang('misc.source')</td>
                                <td>
                                    @if($system != null)
                                        {{ $system->name }}
                                    @else
                                        <i>@lang('misc.unknown')</i>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
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

            <table class="ui compact celled definition table">
                <tbody>
                    <tr>
                        <td>@lang('misc.description')</td>
                        <td>{!! $mutation->describe($detail = true) !!}</td>
                    </tr>
                    <tr>
                        <td>@lang('misc.amount')</td>
                        <td>
                            {!! $mutation->formatAmount(BALANCE_FORMAT_COLOR, ['neutral' => true]) !!}
                            @if($mutation->amount >= 0)
                                @lang('pages.transactions.toTransaction')
                            @else
                                @lang('pages.transactions.fromTransaction')
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('misc.state')</td>
                        <td>{{ $mutation->stateName() }}</td>
                    </tr>
                    <tr>
                        <td>@lang('misc.owner')</td>
                        <td>
                            @if($mutation->owner != null)
                                {{ $mutation->owner->name }}
                            @else
                                <i>@lang('misc.unknownUser')</i>
                            @endif
                        </td>
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
                    <tr>
                        <td>@lang('misc.reference')</td>
                        <td><code class="literal">mutation#{{ $mutation->id }}</div></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
