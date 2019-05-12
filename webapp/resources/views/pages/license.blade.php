@extends('layouts.app')

@section('title', __('pages.license.title'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.license.description')</p>

    <p>
        <a href="https://tldrlegal.com/license/gnu-general-public-license-v3-(gpl-3)"
                target="_blank"
                class="ui button basic">
            @lang('pages.license.licenseTldr')
        </a>
    </p>

    <p><i>@lang('pages.license.onlyEnglishNote')</i></p>

    <div class="ui piled segment">@include('pages.includes.license')</div>

    <h3 class="ui header">@lang('pages.license.questions')</h3>
    <p>@lang('pages.license.questionsDescription')</p>
    <a href="{{ route('license.raw') }}" class="ui button basic">@lang('pages.license.rawLicense')</a>
    <a href="{{ route('contact') }}" class="ui button basic">@lang('pages.contactUs')</a>
@endsection
