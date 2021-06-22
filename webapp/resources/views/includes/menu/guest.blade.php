<a href="{{ route('index') }}"
        class="item {{ Route::currentRouteName() == 'index' ? ' active' : '' }}">
    <i class="glyphicons glyphicons-home"></i>
    @lang('pages.index.title')
</a>
<a href="{{ route('login') }}"
        class="item {{ str_starts_with(Route::currentRouteName(), 'login') ? ' active' : '' }}">
    <i class="glyphicons glyphicons-user"></i>
    @lang('auth.login')
</a>
<a href="{{ route('register') }}"
        class="item {{ Route::currentRouteName() == 'register' ? ' active' : '' }}">
    <i class="glyphicons glyphicons-user-asterisk"></i>
    @lang('auth.register')
</a>
