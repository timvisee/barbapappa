@if(isset($notificationsUnread) && $notificationsUnread->isNotEmpty())
    {{-- TODO: link to user notifications page --}}
    <a href="{{ route('dashboard') }}" class="item header">
        {{-- TODO: translate --}}
        {{ $notificationsUnread->count() }} unread notifications
    </a>
    @foreach($notificationsUnread as $n)
        <a href="{{ route('dashboard') }}" class="item">
            {{ $n->notificationable_type }}
        </a>
        <div class="item">
            test
            <a href="#" class="ui compact inverted tiny button green basic">View</a>
        </div>
    @endforeach
    <div class="item"></div>
@endif

@if(isset($notifications) && $notifications->isNotEmpty())
    {{-- TODO: link to user notifications page --}}
    <a href="{{ route('dashboard') }}" class="item header">
        {{-- TODO: translate --}}
        {{ $notifications->count() }} notifications
    </a>
    @foreach($notifications as $n)
        <a href="{{ route('dashboard') }}" class="item">
            {{ $n->notificationable_type }}
        </a>
    @endforeach
    <div class="item"></div>
@endif

{{-- {1{-- TODO: translate this --}1} --}}
{{-- <div class="item header"><i>No notifications...</i></div> --}}

@if(barauth()->isAuth())
    <a href="{{ route('dashboard') }}" class="item">
        <i class="glyphicons glyphicons-message-full"></i>
        {{-- TODO: translate button --}}
        Messages
    </a>
@endif
