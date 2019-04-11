@extends('layouts.app')

{{-- TODO: remove this? --}}
{{-- @php --}}
{{--     use \App\Models\Transaction; --}}
{{-- @endphp --}}

@section('content')
    <h2 class="ui header">@lang('pages.transactions.titleSingle')</h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.description')</td>
                <td>{{ $transaction->describe() }}</td>
            </tr>
            <tr>
                <td>@lang('misc.amount')</td>
                <td>{!! $transaction->formatCost(BALANCE_FORMAT_COLOR) !!}</td>
            </tr>
            <tr>
                <td>@lang('misc.createdAt')</td>
                <td>{{ $transaction->created_at }}</td>
            </tr>
            @if($transaction->created_at != $transaction->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>{{ $transaction->updated_at }}</td>
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
            {{--         href="{{ route('mutation.show', [ --}}
            {{--             'transactionId' => $mutation->id, --}}
            {{--         ]) }}"> --}}
                    href="#">
                {{ $mutation->describe() }}
                {!! $mutation->formatAmount(BALANCE_FORMAT_LABEL); !!}
            </a>
        @endforeach
    </div>
    <br />

    {{-- TODO: implement go back button! --}}
    {{-- <p> --}}
    {{--     <a href="{{ route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" --}}
    {{--             class="ui button basic"> --}}
    {{--         @lang('general.goBack') --}}
    {{--     </a> --}}
    {{-- </p> --}}
@endsection
