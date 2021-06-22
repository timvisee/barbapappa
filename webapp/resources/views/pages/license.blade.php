@extends('layouts.app')

@section('title', __('pages.license.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('license');
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.license.description')</p>

    <p>
        <a href="https://choosealicense.com/licenses/gpl-3.0/"
                target="_blank"
                class="ui button basic">
            @lang('pages.license.licenseSummary')
        </a>
    </p>
    <div class="ui divider hidden"></div>

    <div class="ui tall stacked segment">@include('pages.includes.license')</div>

    <h3 class="ui header">@lang('pages.license.questions')</h3>
    <p>@lang('pages.license.questionsDescription')</p>
    <a href="{{ route('license.raw') }}" class="ui button basic">@lang('pages.license.plainTextLicense')</a>
    <a href="{{ route('contact') }}" class="ui button basic">@lang('pages.contact.contactUs')</a>
@endsection
