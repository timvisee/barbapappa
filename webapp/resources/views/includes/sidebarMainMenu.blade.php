<div class="ui sidebar mainmenu inverted vertical menu">
    @if(kioskauth()->isAuth())
        @include('includes.menu.kiosk')
    @elseif(barauth()->isAuth())
        @include('includes.menu.user')
    @else
        @include('includes.menu.guest')
    @endif

    @include('includes.menu.footer')
</div>
