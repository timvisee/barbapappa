@extends('layouts.app')

@section('title', __('pages.notifications.title'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.notifications.description')</p>

    {{-- Notification list --}}
    @php
        $groups = [];
        if($notificationsUnread->isNotEmpty())
            $groups[] = [
                'header' => trans_choice('pages.notifications.unread#', $notificationsUnread->count()),
                'notifications' => $notificationsUnread,
                'cardClass' => 'raised',
            ];
        if($notifications->isNotEmpty())
            $groups[] = [
                'header' => trans_choice('pages.notifications.persistent#', $notifications->count()),
                'notifications' => $notifications,
                'cardClass' => 'yellow',
            ];
        if($notificationsRead->isNotEmpty())
            $groups[] = [
                'header' => trans_choice('pages.notifications.read#', $notificationsRead->count()),
                'notifications' => $notificationsRead,
                'disabled' => true,
            ];
    @endphp
    @if(!empty($groups))
        @include('notification.include.list', [
            'groups' => $groups,
        ])
    @endif

    <br />
    <a href="{{ route('dashboard') }}" class="ui button primary">
        @lang('pages.dashboard.title')
    </a>
@endsection
