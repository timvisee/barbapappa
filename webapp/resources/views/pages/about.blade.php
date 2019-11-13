@extends('layouts.app')

@section('title', __('pages.about'))

@section('content')
    <div class="highlight-box">
        <h2 class="ui header">@yield('title')</h2>
        {{ logo()->element(true, ['class' => 'logo']) }}
        <p>{{ config('app.version_name') }} <span style="color: darkgray;">({{ config('app.version_code') }})</span></p>
    </div>

    <div class="align-center">
        <div class="page-divider">
            <span class="line"></span>
            <i class="glyphicons glyphicons-user scale"></i>
            <span class="line"></span>
        </div>

        <p>
            <h5>The project is developed and maintained by</h5>
            <span class="spacer x05"></span>
            <a href="https://timvisee.com" target="_blank">Tim Vis&eacute;e</a>
            <span class="spacer x05"></span>
            <a href="https://timvisee.com" target="_blank">timvisee.com</a>
        </p>

        <div class="page-divider">
            <span class="line"></span>
            <i class="glyphicons glyphicons-lab scale"></i>
            <span class="line"></span>
        </div>

        <p>
            <h5>The source code and a development overview is available at</h5>
            <span class="spacer x05"></span>
            <a href="{{ config('app.source') }}">GitLab</a>
            <span class="spacer x05"></span>
            <a href="https://github.com/timvisee/barbapappa">GitHub (mirror)</a>

            <span class="spacer"></span>

            <h5>Some awesome technologies that have been used are</h5>

            <span class="spacer x05"></span>
            <a href="https://laravel.com/" target="_blank">Laravel</a><br />
            <i>as backend framework</i>

            <span class="spacer x05"></span>
            <a href="https://semantic-ui.com" target="_blank">Semantic UI</a><br />
            <i>as frontend theming framework</i>

            <span class="spacer x05"></span>
            <a href="https://glyphicons.com/" target="_blank">Glyphicons</a>
            <i class="halflings halflings-heart" style="color: #b80000;"></i><br />
            <i>for icons and symbols</i>

            <span class="spacer x05"></span>
            <a href="http://flag-icon-css.lip.is/" target="_blank">flag-icon-css</a>
            <span class="{{ langManager()->getLocaleFlagClass(null, false, true) }}" style="font-size: 0.75em;"></span><br />
            <i>for flag icons</i>

            <span class="spacer x05"></span>
            <a href="https://jquery.com/" target="_blank">jQuery</a><br />
            <i>for simplifying JavaScript</i>

            <span class="spacer"></span>

            <h5>Some resources that have been used are</h5>

            <span class="spacer x05"></span>
            <a href="https://getterms.io/" target="_blank">GetTerms.io</a><br />
            <i>for providing Terms of Service & Privacy Policy</i>

            <span class="spacer x05"></span>
            <a href="http://eloydegen.com/" target="_blank">E. Degen</a><br />
            <i>who suggested the Barbapappa name</i>
        </p>

        <div class="page-divider">
            <span class="line"></span>
            <i class="glyphicons glyphicons-notes-2 scale"></i>
            <span class="line"></span>
        </div>

        <p>
            <h5>Released under the license</h5>
            <span class="spacer x05"></span>
            <a href="{{ route('license') }}">GNU GPL-3.0</a><br />
            <i>(open-source)</i>
        </p>

        <div class="page-divider">
            <span class="line"></span>
            <i class="glyphicons glyphicons-heart-empty scale"></i>
            <span class="line"></span>
        </div>

        <p>
            Thank you for using this product.
            <span class="spacer x05"></span>
            Thank you for being awesome. <i class="halflings halflings-sunglasses" ></i>
        </p>

        <br><br>

        <p>
            Copyright &copy; Barbapappa {{ date('Y') }}.
            <span class="spacer x05"></span>
            All rights reserved.
        </p>
    </div>
@endsection
