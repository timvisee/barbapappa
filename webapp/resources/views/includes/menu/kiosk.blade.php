@php
    if(!isset($r))
        $r = Route::currentRouteName() ?? 'error';
@endphp

{{-- Authenticated header --}}
<div class="item header has-alt-button">
    @lang('misc.kiosk'): {{ kioskauth()->getBar()->name }}
    <a href="{{ route('logout') }}"
            class="alt-button logout"
            title="@lang('auth.logout')">
        <i class="glyphicons glyphicons-exit"></i>
    </a>
</div>

{{-- Menu items --}}
<a href="{{ route('kiosk.main') }}"
        class="item {{ $r == 'kiosk.main' ? ' active' : '' }}">
    <i class="glyphicons glyphicons-shopping-cart"></i>
    @lang('misc.kiosk')
</a>
<a href="{{ route('kiosk.tally') }}"
        class="item {{ $r == 'kiosk.history' ? ' active' : '' }}">
    <i class="glyphicons glyphicons-signal"></i>
    @lang('pages.bar.tallySummary')
</a>
<a href="{{ route('kiosk.history') }}"
        class="item {{ $r == 'kiosk.history' ? ' active' : '' }}">
    <i class="glyphicons glyphicons-eye-open"></i>
    @lang('pages.bar.purchases')
</a>
<a href="{{ route('kiosk.join') }}"
        class="item {{ $r == 'kiosk.join' ? ' active' : '' }}">
    <i class="glyphicons glyphicons-user-add"></i>
    @lang('pages.kioskJoin.title')
</a>
