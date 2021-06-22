<a href="{{ route('dashboard') }}"
        class="item {{ Route::currentRouteName() == 'dashboard' ? ' active' : '' }}">
    <i class="glyphicons glyphicons-dashboard"></i>
    @lang('pages.dashboard.title')
</a>
<a href="{{ route('explore.community') }}" class="item {{ str_starts_with(Route::currentRouteName(), 'explore.') ? ' active' : '' }}">
    <i class="glyphicons glyphicons-search"></i>
    @lang('pages.explore.title')
</a>
<a href="{{ route('account') }}" class="item {{ Route::currentRouteName() == 'account' ? ' active' : '' }}">
    <i class="glyphicons glyphicons-user"></i>
    @lang('pages.account')
</a>
