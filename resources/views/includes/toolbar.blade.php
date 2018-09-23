<div class="toolbar">

    <div class="left">
        <a href="#" class="sidebar-toggle glyphicons glyphicons-menu-hamburger"></a>
    </div>

    <h1>
        @if(barauth()->isAuth())
            <a href="{{ route('dashboard') }}" title="@lang('pages.dashboard')">
                {{ logo()->element(false) }}
            </a>
        @else
            <a href="{{ route('index') }}" title="@lang('pages.dashboard')">
                {{ logo()->element(false) }}
            </a>
        @endif
    </h1>

    <div class="right">
        <a href="{{ route('index') }}" class="glyphicons glyphicons-message-new toolbar-btn-message"></a>
    </div>

</div>
