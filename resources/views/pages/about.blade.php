@extends('layouts.app')

@section('content')

    <div class="highlight-box">
        <h3>@lang('pages.about')</h3>
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
            The project is developed and maintained by
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
            The source code and an overview of the development is available at
            <span class="spacer x05"></span>
            <a href="https://github.com/timvisee/barbapappa">GitHub</a>

            <span class="spacer"></span>

            Some awesome technologies that have been used are

            <span class="spacer x05"></span>
            <a href="https://laravel.com/" target="_blank">Laravel</a>

            <span class="spacer x05"></span>
            <a href="https://glyphicons.com/" target="_blank">Glyphicons</a>
            <i class="halflings halflings-heart" style="color: #b80000;"></i>

            <span class="spacer x05"></span>
            <a href="http://flag-icon-css.lip.is/" target="_blank">flag-icon-css</a>
            <span class="{{ langManager()->getLocaleFlagClass(null, false, true) }}" style="font-size: 0.75em;"></span>

            <span class="spacer x05"></span>
            <a href="https://jquery.com/" target="_blank">jQuery</a>

            <span class="spacer x05"></span>
            <a href="https://jquerymobile.com/" target="_blank">jQuery Mobile</a>
        </p>

        <div class="page-divider">
            <span class="line"></span>
            <i class="glyphicons glyphicons-notes-2 scale"></i>
            <span class="line"></span>
        </div>

        <p>
            Released under the license
            <span class="spacer x05"></span>
            <a href="{{ route('license') }}">GNU GPL-3.0</a><br />
            (Open source)
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

        <div class="page-divider">
            <span class="line"></span>
            <i class="glyphicons glyphicons-copyright-mark scale"></i>
            <span class="line"></span>
        </div>

        <p>
            Copyright &copy; Tim Visée {{ date('Y') }}.
            <span class="spacer x05"></span>
            All rights reserved.
        </p>
    </div>
@endsection
