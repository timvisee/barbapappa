@extends('layouts.app')

@section('title', __('pages.transactions.undoTransaction'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.transactions.undoQuestion')</p>

    {!! Form::open(['action' => ['TransactionController@doUndo', 'transactionId' => $transaction->id, 'force' => $force], 'method' => 'DELETE', 'class' => 'ui form']) !!}

        @if($products->isNotEmpty())

            <div class="ui form">
                <div class="grouped fields {{ ErrorRenderer::hasError('select_products') ? 'error' : '' }}">
                    {{ Form::label('select_products', __('pages.transactions.selectProductsToUndo') . ':') }}
                    @foreach($products as $id => $item)
                        <div class="field">
                            <div class="ui checkbox">
                                <input id="product_{{ $id }}" name="product_{{ $id }}" value="1" type="checkbox" tabindex="0" class="hidden">
                                <label for="product_{{ $id }}">{{ $item['quantity'] }}× {{ $item['product']->displayName() }}</label>
                            </div>
                        </div>
                    @endforeach

                    {{ ErrorRenderer::inline('select_products') }}
                </div>
            </div>

        @else

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
                </tbody>
            </table>

        @endif

        <div class="ui divider hidden"></div>

        <div class="ui buttons">
            <a href="{{ route('transaction.show', ['transactionId' => $transaction->id]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesUndo')</button>
        </div>
    {!! Form::close() !!}
@endsection
