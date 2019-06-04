@extends('layouts.app')

@section('title', __('pages.payments.title'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.payments.description')</p>

    {{-- Payment list --}}
    @php
        $groups = [];
        if($inProgress->isNotEmpty())
            $groups[] = [
                'header' => trans_choice('pages.payments.inProgress#', count($inProgress)),
                'payments' => $inProgress,
            ];
        if($settled->isNotEmpty())
            $groups[] = [
                'header' => trans_choice('pages.payments.settled#', count($settled)),
                'payments' => $settled,
            ];
    @endphp
    @include('payment.include.list', [
        'groups' => $groups,
    ])
@endsection
