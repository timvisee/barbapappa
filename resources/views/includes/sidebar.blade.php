<div data-role="panel" id="sidebar-panel" data-position="left" data-display="overlay">

    <h1>{{ config('app.name') }}</h1>
    <hr>

    <ul data-role="listview">
        <li data-role="list-divider">@lang('pages.pages')</li>
        @if(barauth()->isAuth())
            <li>
                <a href="{{ route('dashboard') }}">@lang('pages.dashboard')</a>
            </li>
            <li>
                <a href="{{ route('account') }}">@lang('pages.account')</a>
            </li>
            <li>
                <a href="{{ route('logout') }}">@lang('auth.logout')</a>
            </li>
        @else
            <li>
                <a href="{{ route('login') }}">@lang('auth.login')</a>
            </li>
            <li>
                <a href="{{ route('register') }}">@lang('auth.register')</a>
            </li>
        @endif

        <li data-role="list-divider">@lang('misc.information')</li>
        <li>
            <a href="{{ route('about') }}">@lang('pages.about')</a>
        </li>
        <li>
            <a href="{{ route('terms') }}">@lang('pages.terms')</a>
        </li>
        <li>
            <a href="{{ route('privacy') }}">@lang('pages.privacy')</a>
        </li>
    </ul>

    {{-- TODO: Better style this language button --}}
    <br />
    <a href="{{ route('language') }}" class="ui-btn ui-corner-all ui-btn-inline">
        <img src="{{ langManager()->getLocaleFlagUrl() }}" />
    </a>

    {{--<a href="#" data-rel="close" class="ui-btn ui-shadow ui-corner-all ui-btn-a ui-icon-delete ui-btn-icon-left ui-btn-inline">Close panel</a>--}}

</div>
