<div data-role="panel" id="sidebar-panel" data-position="left" data-display="overlay">

    <h1>{{ config('app.name') }}</h1>
    <hr>

    <ul data-role="listview">
        <li data-role="list-divider">@lang('pages.pages')</li>
        @if(barauth()->isAuth())
            <li>
                <a href="{{ route('dashboard') }}" class="ui-btn ui-btn-icon-right ui-icon-glyphicons ui-icon-glyphicons-dashboard">@lang('pages.dashboard')</a>
            </li>
            <li>
                <a href="{{ route('account') }}" class="ui-btn ui-btn-icon-right ui-icon-glyphicons ui-icon-glyphicons-user">@lang('pages.account')</a>
            </li>
            <li>
                <a href="{{ route('logout') }}" class="ui-btn ui-btn-icon-right ui-icon-glyphicons ui-icon-glyphicons-exit alert-text-danger">@lang('auth.logout')</a>
            </li>
        @else
            <li>
                <a href="{{ route('index') }}" class="ui-btn ui-btn-icon-right ui-icon-glyphicons ui-icon-glyphicons-home">
                    @lang('pages.dashboard')
                </a>
            </li>
            <li>
                <a href="{{ route('login') }}" class="ui-btn ui-btn-icon-right ui-icon-glyphicons ui-icon-glyphicons-user">
                    @lang('auth.login')
                </a>
            </li>
            <li>
                <a href="{{ route('register') }}" class="ui-btn ui-btn-icon-right ui-icon-glyphicons ui-icon-glyphicons-user-asterisk">@lang('auth.register')</a>
            </li>
        @endif

        <li data-role="list-divider">@lang('misc.information')</li>
        <li>
            <a href="{{ route('about') }}" class="ui-btn ui-btn-icon-right ui-icon-glyphicons ui-icon-glyphicons-heart-empty">@lang('pages.about')</a>
        </li>
        <li>
            <a href="{{ route('terms') }}" class="ui-btn ui-btn-icon-right ui-icon-glyphicons ui-icon-glyphicons-handshake">@lang('pages.terms')</a>
        </li>
        <li>
            <a href="{{ route('privacy') }}" class="ui-btn ui-btn-icon-right ui-icon-glyphicons ui-icon-glyphicons-{{ (langManager()->getLocale() != 'pirate' ? 'fingerprint' : 'skull') }}">@lang('pages.privacy')</a>
        </li>
    </ul>

    {{-- TODO: Better style this language button --}}
    <br />
    <a href="{{ route('language') }}" class="ui-btn ui-corner-all ui-btn-inline">
        {{ langManager()->renderFlag(null, true, true) }}
    </a>

    {{--<a href="#" data-rel="close" class="ui-btn ui-shadow ui-corner-all ui-btn-a ui-icon-delete ui-btn-icon-left ui-btn-inline">Close panel</a>--}}

</div>
