@php
    if(!isset($r))
        $r = Route::currentRouteName() ?? 'error';
@endphp

<a href="{{ route('index') }}"
        class="item {{ $r == 'index' ? ' active' : '' }}">
    <i class="glyphicons glyphicons-home"></i>
    @lang('pages.index.title')
</a>
<a href="{{ route('login') }}"
        class="item {{ str_starts_with($r, 'login') ? ' active' : '' }}">
    <i class="glyphicons glyphicons-user"></i>
    @lang('auth.login')
</a>
<a href="{{ route('register') }}"
        class="item {{ $r == 'register' ? ' active' : '' }}">
    <i class="glyphicons glyphicons-user-asterisk"></i>
    @lang('auth.register')
</a>
