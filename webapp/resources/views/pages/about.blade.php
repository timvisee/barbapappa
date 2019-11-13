@extends('layouts.app')

@section('title', __('pages.about.title'))

@section('content')
    <div class="highlight-box">
        <h2 class="ui header">@yield('title')</h2>
        {{ logo()->element(true, false, ['class' => 'logo']) }}
        <p><i>{{ config('app.version_name') }} <span class="subtle">({{ config('app.version_code') }})</span></i></p>
    </div>

    <div class="margin-center align-center" style="max-width: 400px;">
        <p>@lang('pages.about.description', ['app' => config('app.name')])</p>
        <div class="ui list">
            <a href="{{ route('contact') }}" class="item">@lang('pages.contact.contactUs')</a>
        </div>

        <h3 class="ui horizontal divider header">
            <i class="glyphicons glyphicons-user scale"></i>
        </h3>

        <p>@lang('pages.about.developedBy'):</p>
        <div class="ui list">
            <div class="item">
                <a href="https://timvisee.com" target="_blank">Tim Visée</a>
                —
                <a href="https://timvisee.com" target="_blank">timvisee.com</a>
            </div>
        </div>

        <h3 class="ui horizontal divider header">
            <i class="glyphicons glyphicons-lab scale"></i>
        </h3>

        <p>@lang('pages.about.sourceDescription')</p>

        <p>@lang('pages.about.sourceAt'):</p>
        <div class="ui list">
            <a class="item" href="{{ config('app.source') }}" target="_blank">GitLab</a>
            <div class="item">
                <a class="item" href="https://github.com/timvisee/barbapappa" target="_blank">GitHub</a>
                <span class="subtle">(mirror)</a>
            </div>
        </div>

        <p>@lang('pages.about.withLicense'):</p>
        <div class="ui list">
            <a href="{{ route('license') }}" class="item">GNU GPL-3.0</a>
        </div>

        <h3 class="ui horizontal divider header">
            <i class="glyphicons glyphicons-star-empty scale"></i>
        </h3>

        <p>@lang('pages.about.usedTechnologies'):</p>
        <div class="ui list">
            <div class="item">
                <a href="https://laravel.com/" target="_blank">Laravel</a>
                —
                <i>@lang('pages.about.noteLaravel')</i>
            </div>
            <div class="item">
                <a href="https://semantic-ui.com" target="_blank">Semantic UI</a>
                —
                <i>@lang('pages.about.noteSemanticUi')</i>
            </div>
            <div class="item">
                <a href="https://glyphicons.com/" target="_blank">Glyphicons</a>
                —
                <i>@lang('pages.about.noteGlyphicons')</i>&nbsp;
                <i class="halflings halflings-heart" style="color: #b80000;"></i>
            </div>
            <div class="item">
                <a href="http://flag-icon-css.lip.is/" target="_blank">flag-icon-css</a>
                —
                <i>@lang('pages.about.noteFlags')</i>&nbsp;
                <span class="{{ langManager()->getLocaleFlagClass(null, false, true) }}" style="font-size: 0.75em;"></span><br />
            </div>
            <div class="item">
                <a href="https://jquery.com/" target="_blank">jQuery</a>
                —
                <i>@lang('pages.about.noteJQuery')</i>
            </div>
        </div>

        <p>@lang('pages.about.otherResources'):</p>
        <div class="ui list">
            <div class="item">
                <a href="https://getterms.io/" target="_blank">GetTerms.io</a>
                —
                <i>@lang('pages.about.noteGetTerms')</i>
            </div>
            <div class="item">
                <a href="http://eloydegen.com/" target="_blank">E. Degen</a>
                —
                <i>@lang('pages.about.noteEDegen')</i>
            </div>
        </div>

        <h3 class="ui horizontal divider header">
            <i class="glyphicons glyphicons-donate scale"></i>
        </h3>

        <p>@lang('pages.about.donate')</p>
        <div class="ui list">
            <a href="https://timvisee.com/donate" target="_blank" class="item">
                @lang('misc.donate')
            </a>
        </div>

        <h3 class="ui horizontal divider header">
            <i class="glyphicons glyphicons-heart-empty scale"></i>
        </h3>

        <p>
            @lang('pages.about.thanks')
            <i class="halflings halflings-sunglasses" ></i>
        </p>

        <br>

        <p>
            @lang('pages.about.copyright', [
                'app' => config('app.name'),
                'year' => date('Y'),
            ])
        </p>
    </div>
@endsection
