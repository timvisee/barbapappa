<div class="ui sidebar mainmenu inverted vertical menu">
    @if(barauth()->isAuth())
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
        <a href="{{ route('dashboard') }}" class="item">
            <i class="glyphicons glyphicons-dashboard"></i>
            @lang('pages.dashboard')
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

    {{-- Page specific links --}}
    @if(!empty($menulinks))
        <div class="item">
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
