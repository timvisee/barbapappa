@extends('layouts.app')

@section('title', __('pages.transactions.undoTransaction'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.transactions.undoQuestion')</p>

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

    <br />

    {!! Form::open(['action' => ['TransactionController@doUndo', 'transactionId' => $transaction->id], 'method' => 'DELETE', 'class' => 'ui form']) !!}
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
