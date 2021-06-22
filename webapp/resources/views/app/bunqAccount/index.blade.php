@extends('layouts.app')

@section('title', __('pages.bunqAccounts.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('app.bunqaccount.index');
    $menusection = 'app_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>@lang('pages.bunqAccounts.description')</p>

    <div class="ui vertical menu fluid">
        @forelse($accounts as $account)
            {{-- TODO: link to bunq account page --}}
            <a href="{{ route('app.bunqAccount.show', [
                'accountId' => $account->id
            ]) }}" class="item">
                {{ $account->name }}
            </a>
        @empty
            <div class="item">
                {{-- TODO: translate --}}
                <i>@lang('pages.bunqAccounts.noAccounts')</i>
            </div>
        @endforelse
    </div>

    <a href="{{ route('app.bunqAccount.create') }}"
            class="ui button basic positive">
        @lang('misc.add')
    </a>

    <a href="{{ route('app.manage') }}"
            class="ui button basic">
        @lang('general.goBack')
    </a>
@endsection
