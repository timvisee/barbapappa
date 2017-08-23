{{-- Optional header style: border-top: none; border-bottom: 1px solid #CCCCCC; --}}

<div id="header" data-role="header">

    <a id="sidebar-toggle" href="#sidebar-panel" class="ui-btn ui-corner-all ui-btn-icon-notext ui-icon-bars">MENU</a>

    {{--<a href="" class="ui-btn ui-corner-all ui-btn-icon-notext ui-icon-delete" data-rel="back">CLOSE</a>--}}

    <h1>
        <a href="{{ route('index') }}" data-ajax="false" title="Refresh app">
            {{-- TODO: Use a properly sized image here --}}
            <img src="{{ asset('img/logo/logo_header_big.png') }}" style="height: 21px;" />
        </a>
    </h1>

</div>
