@extends('layouts.app')

@section('title', __('pages.privacy.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('privacy');
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.privacy.description')</p>

    <div class="ui divider hidden"></div>

    <div class="ui tall stacked segment">@include('pages.includes.privacyPolicy')</div>

    <h3 class="ui header">@lang('pages.privacy.questions')</h3>
    <p>@lang('pages.privacy.questionsDescription')</p>
    <a href="{{ route('contact') }}" class="ui button basic">@lang('pages.contact.contactUs')</a>
@endsection
