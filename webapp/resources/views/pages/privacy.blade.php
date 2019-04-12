@extends('layouts.app')

@section('title', __('pages.privacy.title'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.privacy.description')</p>

    <p><i>@lang('pages.privacy.onlyEnglishNote')</i></p>

    <div class="ui piled segment">@include('pages.includes.privacyPolicy')</div>

    <h3 class="ui header">@lang('pages.privacy.questions')</h3>
    <p>@lang('pages.privacy.questionsDescription')</p>
    <a href="{{ route('contact') }}" class="ui button basic">@lang('pages.contactUs')</a>
@endsection
