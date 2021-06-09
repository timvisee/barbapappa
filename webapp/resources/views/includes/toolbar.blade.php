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
            {{ logo()->element(false, false) }}
        </a>
    </h1>

    <div class="right">
        @if(kioskauth()->isAuth())
            <a href="{{ route('kiosk.join') }}" class="glyphicons glyphicons-user-add"></a>
            <a href="{{ route('kiosk.main') }}" class="glyphicons glyphicons-shop"></a>
        @elseif(barauth()->isAuth())
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
        @else
            <a href="{{ route('login') }}"
                class="glyphicons glyphicons-user-key"></a>
        @endif
    </div>

</div>
