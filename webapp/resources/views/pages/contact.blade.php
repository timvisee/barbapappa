@extends('layouts.app')

@section('title', __('pages.contact.title'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>@lang('pages.contact.description')</p>

    <div class="ui bulleted list">
        <span class="item">
            Email:
            <a href="mailto:3a4fb3964f@sinenomine.email">3a4fb3964f@sinenomine.email</a>
        </span>
        <span class="item">
            Website:
            <a href="https://timvisee.com/contact">https://timvisee.com/contact</a>
        </span>
    </div>

    <p>@lang('pages.contact.issuesDescription')</p>

    <div class="ui bulleted list">
        <span class="item">
            Issues:
            <a href="https://gitlab.com/timvisee/barbapappa/issues/">https://gitlab.com/timvisee/barbapappa/issues/</a>
        </span>
        <span class="item">
            New issue email:
            <a href="mailto:incoming+timvisee-barbapappa-4423731-issue-@incoming.gitlab.com">incoming+timvisee-barbapappa-4423731-issue-@incoming.gitlab.com</a>
        </span>
    </div>
@endsection
