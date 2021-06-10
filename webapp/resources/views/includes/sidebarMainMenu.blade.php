<div class="ui sidebar mainmenu inverted vertical menu">
    @if(kioskauth()->isAuth())
        <div class="item header has-alt-button">
            @lang('misc.kiosk'): {{ kioskauth()->getBar()->name }}
            <a href="{{ route('logout') }}"
                    class="alt-button logout"
                    title="@lang('auth.logout')">
                <i class="glyphicons glyphicons-exit"></i>
            </a>
        </div>
        <a href="{{ route('kiosk.main') }}"
                class="item {{ Route::currentRouteName() == 'kiosk.main' ? ' active' : '' }}">
            <i class="glyphicons glyphicons-shop"></i>
            @lang('misc.kiosk')
        </a>
        <a href="{{ route('kiosk.history') }}"
                class="item {{ Route::currentRouteName() == 'kiosk.history' ? ' active' : '' }}">
            <i class="glyphicons glyphicons-history"></i>
            @lang('pages.bar.purchaseHistory')
        </a>
        <a href="{{ route('kiosk.join') }}"
                class="item {{ Route::currentRouteName() == 'kiosk.join' ? ' active' : '' }}">
            <i class="glyphicons glyphicons-user-add"></i>
            @lang('pages.kioskJoin.title')
        </a>
    @elseif(barauth()->isAuth())
        <div class="item header has-alt-button">
            {{ trans_random('general.hellos') }}
            {{ barauth()->getSessionUser()->name }}

            <a href="{{ route('logout') }}"
                    class="alt-button logout"
                    title="@lang('auth.logout')">
                <i class="glyphicons glyphicons-exit"></i>
            </a>
        </div>
        <a href="{{ route('last') }}" class="item">
            <i class="glyphicons glyphicons-undo"></i>
            @lang('pages.last.title')
        </a>
        <a href="{{ route('dashboard') }}"
                class="item {{ Route::currentRouteName() == 'dashboard' ? ' active' : '' }}">
            <i class="glyphicons glyphicons-dashboard"></i>
            @lang('pages.dashboard.title')
        </a>
    @else
        <a href="{{ route('index') }}"
                class="item {{ Route::currentRouteName() == 'index' ? ' active' : '' }}">
            <i class="glyphicons glyphicons-home"></i>
            @lang('pages.index.title')
        </a>
        <a href="{{ route('login') }}"
                class="item {{ Route::currentRouteName() == 'login' ? ' active' : '' }}">
            <i class="glyphicons glyphicons-user"></i>
            @lang('auth.login')
        </a>
        <a href="{{ route('register') }}"
                class="item {{ Route::currentRouteName() == 'register' ? ' active' : '' }}">
            <i class="glyphicons glyphicons-user-asterisk"></i>
            @lang('auth.register')
        </a>
    @endif

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

    @if(barauth()->isAuth())
        <a href="{{ route('explore.community') }}" class="item">
            <i class="glyphicons glyphicons-search"></i>
            @lang('pages.explore.title')
        </a>
        <a href="{{ route('account') }}" class="item">
            <i class="glyphicons glyphicons-user"></i>
            @lang('pages.account')
        </a>
    @endif

    {{-- Generic information links --}}
    <div class="item">
        <div class="header">@lang('misc.information')</div>
        <div class="menu">
            <div class="item link pwa-install-button">
                <i class="glyphicons glyphicons-download"></i>
                @lang('app.installApp')
            </div>
            <a href="{{ route('about') }}"
                    class="item {{ Route::currentRouteName() == 'about' ? ' active' : '' }}">
                <i class="glyphicons glyphicons-heart-empty"></i>
                @lang('pages.about.title')
            </a>
            <a href="{{ route('terms') }}"
                    class="item {{ Route::currentRouteName() == 'terms' ? ' active' : '' }}">
                <i class="glyphicons glyphicons-handshake"></i>
                @lang('pages.terms.title')
            </a>
            <a href="{{ route('privacy') }}"
                    class="item {{ Route::currentRouteName() == 'privacy' ? ' active' : '' }}">
                <i class="glyphicons glyphicons-{{ (langManager()->getLocale() != 'pirate' ? 'fingerprint' : 'skull') }}"></i>
                @lang('pages.privacy.title')
            </a>
            <a href="{{ route('license') }}" class="item">
                <i class="glyphicons glyphicons-scale-classic"></i>
                @lang('pages.license.title')
            </a>
            <a href="{{ route('contact') }}"
                    class="item {{ Route::currentRouteName() == 'contact' ? ' active' : '' }}">
                <i class="glyphicons glyphicons-send"></i>
                @lang('pages.contact.title')
            </a>
            <a href="{{ route('language') }}"
                    class="item {{ Route::currentRouteName() == 'language' ? ' active' : '' }}">
                @lang('lang.language')
                {{ langManager()->renderFlag(null, true, true) }}
            </a>
        </div>
    </div>

    {{-- Pirate language easter egg --}}
    @if(Route::currentRouteName() == 'about' || rand_float() <= (float) config('app.pirate_chance'))
        <a href="{{ route('language', [
                    'locale' => 'pirate',
                    'redirect' => url()->full(),
                ]) }}"
                title="Yarrrr!"
                class="pirate popup"
                data-content="Yarrrr!"
                data-offset="-16"
                data-variation="inverted">
            <img src="{{ asset('img/pirate.png') }}" />
        </a>
    @endif

</div>
