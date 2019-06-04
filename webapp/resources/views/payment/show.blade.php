@extends('layouts.app')

@section('title', __('pages.payments.details'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    @if($payment->isInProgress())
        <div class="ui info message visible">
            <div class="header">@lang('pages.payments.inProgress')</div>
            <p>@lang('pages.payments.inProgressDescription')</p>
            <a href="{{ route('payment.pay', ['paymentId' => $payment->id]) }}"
                    class="ui button basic">
                @lang('misc.showProgress')
            </a>
        </div>
    @endif

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.amount')</td>
                <td>{!! $payment->formatCost(BALANCE_FORMAT_COLOR) !!}</td>
            </tr>
            <tr>
                <td>@lang('misc.state')</td>
                <td>{{ $payment->stateName() }}</td>
            </tr>
            <tr>
                <td>@lang('misc.initiatedAt')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $payment->created_at])</td>
            </tr>
            @if($payment->created_at != $payment->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $payment->updated_at])</td>
                </tr>
            @endif
            <tr>
                <td>@lang('pages.paymentService.serviceType')</td>
                <td>{{ $payment->service->displayName() }}</td>
            </tr>
        </tbody>
    </table>

    {{-- TODO: link wallet and transaction related to this payment --}}

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

    {{-- TODO: implement go back button! --}}
    {{-- <p> --}}
    {{--     <a href="{{ route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" --}}
    {{--             class="ui button basic"> --}}
    {{--         @lang('general.goBack') --}}
    {{--     </a> --}}
    {{-- </p> --}}
@endsection
