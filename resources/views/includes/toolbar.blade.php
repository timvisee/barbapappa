<div class="toolbar">

    <div class="left">
        <a href="#"
            class="sidebar-toggle glyphicons glyphicons-menu-hamburger"
            data-sidebar="mainmenu"></a>
    </div>

    <h1>
        @php
            $homeRoute = barauth()->isAuth() ? 'dashboard' : 'index';
        @endphp

        <a href="{{ route($homeRoute) }}" title="@lang('pages.' . $homeRoute)">
            {{ logo()->element(false) }}
        </a>
    </h1>

    <div class="right">
        <a href="#"
            class="sidebar-toggle glyphicons glyphicons-message-new toolbar-btn-message"
            data-sidebar="messages"></a>
    </div>

</div>
