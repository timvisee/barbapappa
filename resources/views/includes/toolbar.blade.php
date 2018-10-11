<div class="toolbar">

    <div class="left">
        <a href="#" class="sidebar-toggle glyphicons glyphicons-menu-hamburger"></a>
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
        <a href="{{ route('index') }}" class="glyphicons glyphicons-message-new toolbar-btn-message"></a>
    </div>

</div>
