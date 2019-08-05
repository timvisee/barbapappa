<div class="toolbar">

    <div class="left">
        <a href="#"
            class="sidebar-toggle glyphicons glyphicons-menu-hamburger"
            data-sidebar="mainmenu"></a>
    </div>

    <h1>
        @php
            $homeRoute = barauth()->isAuth() ? 'dashboard' : 'index';
            $homeRouteName = __('pages.' . (barauth()->isAuth() ?  'dashboard' : 'index') . '.title');
        @endphp

        <a href="{{ route($homeRoute) }}" title="{{ $homeRouteName }}">
            {{ logo()->element(false) }}
        </a>
    </h1>

    <div class="right">
        @if(isset($notificationCounts) && $notificationCounts['unread'] > 0)
            <a href="#"
                    class="ui red circular tiny label sidebar-toggle"
                    data-sidebar="messages">
                {{ $notificationCounts['unread'] }}
            </a>
        @elseif(isset($notificationCounts) && $notificationCounts['persistent'] > 0)
            <a href="#"
                    class="ui blue circular tiny label sidebar-toggle"
                    data-sidebar="messages">
                {{ $notificationCounts['persistent'] }}
            </a>
        @else
            <a href="#"
                class="sidebar-toggle glyphicons glyphicons-message-new toolbar-btn-message"
                data-sidebar="messages"></a>
        @endif
    </div>

</div>
