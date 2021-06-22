{{-- Authenticated header --}}
<div class="item header has-alt-button">
    {{ trans_random('general.hellos') }}
    {{ barauth()->getSessionUser()->name }}

    <a href="{{ route('logout') }}"
            class="alt-button logout"
            title="@lang('auth.logout')">
        <i class="glyphicons glyphicons-exit"></i>
    </a>
</div>

{{-- Menu items --}}
<a href="{{ route('last') }}" class="item">
    <i class="glyphicons glyphicons-undo"></i>
    @lang('pages.last.title')
</a>
<a href="{{ route('dashboard') }}"
        class="item {{ Route::currentRouteName() == 'dashboard' ? ' active' : '' }}">
    <i class="glyphicons glyphicons-dashboard"></i>
    @lang('pages.dashboard.title')
</a>

{{-- TODO: remove this --}}
{{-- Page specific links --}}
@if(!empty($menulinks))
    <div class="item active">
        <div class="header">@yield('title')</div>
        <div class="menu">
            @foreach($menulinks as $menulink)
                <a href="{{ $menulink['link'] }}" class="item">
                    <i class="glyphicons glyphicons-{{ $menulink['icon'] ?? 'chevron-right' }}"></i>
                    {{ $menulink['name'] }}
                </a>
            @endforeach
        </div>
    </div>
@endif

<div class="item header spaced">Site:</div>
@include('includes.menu.section.site')
