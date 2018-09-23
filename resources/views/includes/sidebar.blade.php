<div class="ui sidebar inverted vertical menu">
    @if(barauth()->isAuth())
        <a href="{{ route('dashboard') }}" class="item">
            <i class="icon-glyphicons glyphicons glyphicons-dashboard"></i>
            @lang('pages.dashboard')
        </a>
        <a href="{{ route('account') }}" class="item">
            <i class="icon-glyphicons glyphicons glyphicons-user"></i>
            @lang('pages.account')
        </a>
        <a href="{{ route('logout') }}" class="item red">
            <i class="icon-glyphicons glyphicons glyphicons-exit"></i>
            @lang('auth.logout')
        </a>
    @else
        <a href="{{ route('index') }}" class="item">
            <i class="icon-glyphicons glyphicons glyphicons-dashboard"></i>
            @lang('pages.dashboard')
        </a>
        <a href="{{ route('login') }}" class="item">
            <i class="icon-glyphicons glyphicons glyphicons-user"></i>
            @lang('auth.login')
        </a>
        <a href="{{ route('register') }}" class="item">
            <i class="icon-glyphicons glyphicons glyphicons-user-asterisk"></i>
            @lang('auth.register')
        </a>
    @endif

    <div class="item">
        <div class="header">@lang('misc.information')</div>
        <div class="menu">
            <a href="{{ route('about') }}" class="item">
                <i class="icon-glyphicons glyphicons glyphicons-heart-empty"></i>
                @lang('pages.about')
            </a>
            <a href="{{ route('terms') }}" class="item">
                <i class="icon-glyphicons glyphicons glyphicons-handshake"></i>
                @lang('pages.terms')
            </a>
            <a href="{{ route('privacy') }}" class="item">
                <i class="icon-glyphicons glyphicons glyphicons-{{ (langManager()->getLocale() != 'pirate' ? 'fingerprint' : 'skull') }}"></i>
                @lang('pages.privacy')
            </a>
            <a href="{{ route('contact') }}" class="item">
                <i class="icon-glyphicons glyphicons glyphicons-send"></i>
                @lang('pages.contact')
            </a>
            <a href="{{ route('language') }}" class="item">
                @lang('lang.language')
                {{ langManager()->renderFlag(null, true, true) }}
            </a>
        </div>
    </div>
</div>
