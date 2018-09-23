<div class="toolbar">

    <div class="left">
        <a href="#" class="sidebar-toggle glyphicons glyphicons-menu-hamburger"></a>
    </div>

    <h1>
        <a href="{{ route('index') }}" title="Refresh app">
            {{-- TODO: Use a properly sized image here --}}
            {{ logo()->element(false) }}
        </a>
    </h1>

    <div class="right">
        <a href="{{ route('index') }}" class="glyphicons glyphicons-message-new toolbar-btn-message"></a>
    </div>

</div>
