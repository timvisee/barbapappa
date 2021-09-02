@extends('layouts.app')

@section('title', __('pages.bar.kioskManagement'))
@php
    $breadcrumbs = Breadcrumbs::generate('bar.manage.kiosk', $bar);
    $menusection = 'bar_manage';

    use App\Http\Controllers\BarController;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">@lang('misc.kiosk')</h5>
        @if(perms(BarController::permsManage()))
            <a href="{{ route('bar.kiosk.start', ['barId' => $bar->human_id]) }}" class="item">
                @lang('pages.bar.startKiosk')
            </a>
        @else
            <div class="item disabled">@lang('pages.bar.startKiosk')</div>
        @endif
        @if(perms(BarController::permsManage()))
            <a href="{{ route('bar.kiosk.sessions.index', ['barId' => $bar->human_id]) }}" class="item">
                @lang('pages.bar.kioskSessions')
            </a>
        @else
            <div class="item disabled">@lang('pages.bar.kioskSessions')</div>
        @endif
    </div>

    <a href="{{ route('bar.manage', ['barId' => $bar->human_id]) }}"
            class="ui button basic">
        @lang('pages.bar.backToBar')
    </a>
@endsection
