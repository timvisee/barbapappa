{{-- Optional header style: border-top: none; border-bottom: 1px solid #CCCCCC; --}}

<div id="header" data-role="header">

    <div class="left">
        <a id="sidebar-toggle" href="#sidebar-panel" class="glyphicons glyphicons-menu-hamburger"></a>
    </div>

    <h1>
        <a href="{{ route('index') }}" data-ajax="false" title="Refresh app">
            {{-- TODO: Use a properly sized image here --}}
            {{ logo()->element(false, ['style' => 'height: 21px; display: block;']) }}
        </a>
    </h1>

    <div class="right">
        <a href="{{ route('index') }}" class="glyphicons glyphicons-message-new toolbar-btn-message"></a>
    </div>

</div>
