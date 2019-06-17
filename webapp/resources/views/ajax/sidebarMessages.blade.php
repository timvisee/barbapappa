@if(isset($notificationsUnread) && $notificationsUnread->isNotEmpty())
    {{-- TODO: link to user notifications page --}}
    <a href="{{ route('dashboard') }}" class="item header">
        {{-- TODO: translate --}}
        {{ $notificationsUnread->count() }} unread notifications
    </a>
    @foreach($notificationsUnread as $notification)
        @include('ajax.include.notification', $notification->viewData())
    @endforeach
    <div class="item"></div>
@endif

@if(isset($notifications) && $notifications->isNotEmpty())
    {{-- TODO: link to user notifications page --}}
    <a href="{{ route('dashboard') }}" class="item header">
        {{-- TODO: translate --}}
        {{ $notifications->count() }} notifications
    </a>
    @foreach($notifications as $notification)
        @include('ajax.include.notification', $notification->viewData())
    @endforeach
    <div class="item"></div>
@endif

@if((!isset($notificationsUnread) || $notificationsUnread->isEmpty())
    && (!isset($notifications) || $notifications->isEmpty()))
    {{-- TODO: translate this --}}
    <div class="item"><i>No notifications...</i></div>
@endif

{{-- TODO: route to notifications page --}}
<a href="{{ route('dashboard') }}" class="item">
    <i class="glyphicons glyphicons-message-full"></i>
    {{-- TODO: translate --}}
    All notifications
</a>
