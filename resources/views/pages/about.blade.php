@extends('layouts.app')

@section('content')

    <p>
        Some text here.
    </p>

    <div class="page-divider">
        <span class="line"></span>
    </div>

    <p>
        Some text here.
    </p>

    <div class="page-divider">
        <span class="line"></span>
        <span class="line"></span>
        <span class="slot">Some text here</span>
    </div>

    <p>
        Some text here.
    </p>

    <div class="page-divider">
        <span class="line"></span>
        <span class="line"></span>
        <img src="{{ asset('img/logo/logo_header_big.png') }}" style="height: 21px;" class="slot" />
    </div>

    <p>
        Some text here.
    </p>

    <div class="page-divider">
        <span class="line"></span>
        <i class="glyphicons glyphicons-sunglasses scale"></i>
        <span class="line"></span>
    </div>

    <p>
        Some text here.
    </p>

    <div class="page-divider">
        <span class="line"></span>
        <i class="glyphicons glyphicons-lab scale"></i>
        <span class="line"></span>
    </div>

    <p>
        Some text here.
    </p>

    <div class="page-divider">
        <span class="line"></span>
        <i class="glyphicons glyphicons-heart-empty scale"></i>
        <span class="line"></span>
    </div>

    <p>
        Thank you for using this product.<br />
        Thank you for being awesome. <i class="halflings halflings-sunglasses" ></i>
    </p>


    <br>
    <br>
    <br>
    <br>
    <br>
    <br>

    <div class="highlight-box">
        {{--<i class="glyphicons glyphicons-family attention-icon"></i>--}}
        {{--<br />--}}
        <h3>@lang('pages.about')</h3>
        <img src="{{ asset('img/logo/logo_big.png') }}" class="logo" />
    </div>

    <br>
    <br>
    <br>
    <br>
    <br>

    <h1>@lang('pages.about')</h1>
    [About page placeholder]
@endsection
