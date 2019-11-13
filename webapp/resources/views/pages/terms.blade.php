@extends('layouts.app')

@section('title', __('pages.terms.title'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.terms.description')</p>

    <p><i>@lang('pages.terms.onlyEnglishNote')</i></p>

    <div class="ui divider hidden"></div>

    <div class="ui tall stacked segment">@include('pages.includes.termsOfService')</div>

    <h3 class="ui header">@lang('pages.terms.questions')</h3>
    <p>@lang('pages.terms.questionsDescription')</p>
    <a href="{{ route('contact') }}" class="ui button basic">@lang('pages.contact.contactUs')</a>
@endsection
