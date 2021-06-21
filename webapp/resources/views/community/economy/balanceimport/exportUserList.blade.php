@extends('layouts.app')

@section('title', __('pages.balanceImport.exportUserList'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.balanceimport.show', $system);
@endphp

@php
    use \App\Http\Controllers\BalanceImportSystemController;

    // Define menulinks
    $menulinks[] = [
        'name' => __('general.goBack'),
        'link' => route('community.economy.balanceimport.event.index', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'systemId' => $system->id,
        ]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>@lang('pages.balanceImport.exportUserListDescription', [
        'app' => config('app.name')
    ])</p>

    <pre class="copy ui segment"
            data-copy="{{ $data }}"
            style="max-height: 250px; overflow: auto;"
            >{{ $data }}</pre>

    <p>
        <a href="{{ route('community.economy.balanceimport.event.index', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'systemId' => $system->id,
                ]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
