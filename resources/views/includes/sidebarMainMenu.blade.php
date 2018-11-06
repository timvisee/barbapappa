<div class="ui sidebar mainmenu inverted vertical menu">
    @if(barauth()->isAuth())
        <a href="{{ route('dashboard') }}" class="item">
            <i class="glyphicons glyphicons-dashboard"></i>
            @lang('pages.dashboard')
        </a>
        <a href="{{ route('community.overview') }}" class="item">
            <i class="glyphicons glyphicons-group"></i>
            @lang('pages.communities')
        </a>
        <a href="{{ route('bar.overview') }}" class="item">
            <i class="glyphicons glyphicons-beer"></i>
            @lang('pages.bars')
        </a>
        <a href="{{ route('account') }}" class="item">
            <i class="glyphicons glyphicons-user"></i>
            @lang('pages.account')
        </a>
        <a href="{{ route('logout') }}" class="item red">
            <i class="glyphicons glyphicons-exit"></i>
            @lang('auth.logout')
        </a>
    @else
        <a href="{{ route('index') }}" class="item">
            <i class="glyphicons glyphicons-dashboard"></i>
            @lang('pages.dashboard')
        </a>
        <a href="{{ route('login') }}" class="item">
            <i class="glyphicons glyphicons-user"></i>
            @lang('auth.login')
        </a>
        <a href="{{ route('register') }}" class="item">
            <i class="glyphicons glyphicons-user-asterisk"></i>
            @lang('auth.register')
        </a>
    @endif

    <div class="item">
        <div class="header">@lang('misc.information')</div>
        <div class="menu">
            <a href="{{ route('about') }}" class="item">
                <i class="glyphicons glyphicons-heart-empty"></i>
                @lang('pages.about')
            </a>
            <a href="{{ route('terms') }}" class="item">
                <i class="glyphicons glyphicons-handshake"></i>
                @lang('pages.terms.title')
            </a>
            <a href="{{ route('privacy') }}" class="item">
                <i class="glyphicons glyphicons-{{ (langManager()->getLocale() != 'pirate' ? 'fingerprint' : 'skull') }}"></i>
                @lang('pages.privacy.title')
            </a>
            <a href="{{ route('license') }}" class="item">
                <i class="glyphicons glyphicons-scale-classic"></i>
                @lang('pages.license.title')
            </a>
            <a href="{{ route('contact') }}" class="item">
                <i class="glyphicons glyphicons-send"></i>
                @lang('pages.contact')
            </a>
            <a href="{{ route('language') }}" class="item">
                @lang('lang.language')
                {{ langManager()->renderFlag(null, true, true) }}
            </a>
        </div>
    </div>
</div>
