@extends('layouts.app')

@section('title', __('pages.notifications.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('notification.index');
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.notifications.description')</p>

    {{-- Unread notification list --}}
    @if($notificationsUnread->isNotEmpty())
        @include('notification.include.list', [
            'groups' => [[
                'header' => trans_choice('pages.notifications.unread#', $notificationsUnread->count()),
                'notifications' => $notificationsUnread,
                'cardClass' => 'raised',
            ]],
        ])
        <br />

        {{-- Mark all as read button --}}
        {!! Form::open([
            'action' => ['NotificationController@doMarkAllRead'],
            'method' => 'POST',
            'class' => 'ui form'
        ]) !!}
            <button class="ui tiny button positive basic" type="submit">@lang('pages.notifications.markAllAsRead')</button>
        {!! Form::close() !!}
    @endif

    {{-- Other notification list --}}
    @php
        $groups = [];
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
