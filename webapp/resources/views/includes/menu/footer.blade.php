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
        <a href="{{ route('license') }}"
                class="item{{ Route::currentRouteName() == 'license' ? ' active' : '' }}">
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
            <span class="right">{{ langManager()->renderFlag(null, true, true) }}</span>
        </a>
    </div>
</div>

{{-- Breadcrumbs --}}
@if(isset($breadcrumbs) && $breadcrumbs->count() > 1)
    <div class="item">
        <div class="header">@lang('misc.breadcrumbTrail')</div>
        <div class="menu">
            <a href="{{ $breadcrumbs[0]->url }}" class="item">
                <i class="glyphicons glyphicons-home"></i>
                {{ $breadcrumbs[0]->title }}
            </a>
            @foreach($breadcrumbs->skip(1) as $breadcrumb)
                @if($breadcrumb->url)
                    <a href="{{ $breadcrumb->url }}" class="item {{ $loop->last ? 'active' : '' }}">
                        <i class="glyphicons {{ $loop->last ? 'glyphicons-ok-circle' : 'glyphicons-download' }}"></i>
                        <span class="subtle">&#8627;</span>
                        {{ $breadcrumb->title }}
                    </a>
                @else
                    <div class="item {{ $loop->last ? 'active' : '' }}">
                        <i class="glyphicons {{ $loop->last ? 'glyphicons-ok-circle' : 'glyphicons-download' }}"></i>
                        <span class="subtle">&#8627;</span>
                        {{ $breadcrumb->title }}
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endif

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
