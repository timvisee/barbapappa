@extends('layouts.app')

@section('content')
    <h2 class="ui header">@lang('pages.terms.title')</h2>
    <p>@lang('pages.terms.description')</p>

    <p><i>@lang('pages.terms.onlyEnglishNote')</i></p>

    <div class="ui piled segment">@include('pages.includes.termsOfService')</div>

    <h3 class="ui header">@lang('pages.terms.questions')</h3>
    <p>@lang('pages.terms.questionsDescription')</p>
    <a href="{{ route('contact') }}" class="ui button basic">@lang('pages.contactUs')</a>
@endsection
