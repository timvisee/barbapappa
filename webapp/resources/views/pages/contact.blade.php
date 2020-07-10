@extends('layouts.app')

@section('title', __('pages.contact.title'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>@lang('pages.contact.description')</p>

    <div class="ui bulleted list">
        <span class="item">
            @lang('misc.mail'):
            <a href="mailto:3a4fb3964f@sinenomine.email">3a4fb3964f@sinenomine.email</a>
        </span>
        <span class="item">
            @lang('misc.developer'):
            <a href="https://timvisee.com/contact">https://timvisee.com/contact</a>
        </span>
    </div>

    <div class="ui divider hidden"></div>

    <div class="ui fluid accordion">
        <div class="title">
            <i class="dropdown icon"></i>
            @lang('pages.about.thisAppIsOpenSource')
        </div>
        <div class="content">
            <p>@lang('pages.contact.issuesDescription')</p>

            <div class="ui bulleted list">
                <span class="item">
                    @lang('misc.sourceCode'):
                    <a href="https://gitlab.com/timvisee/barbapappa/">https://gitlab.com/timvisee/barbapappa/</a>
                </span>
                <span class="item">
                    @lang('pages.contact.issueList'):
                    <a href="https://gitlab.com/timvisee/barbapappa/issues/">https://gitlab.com/timvisee/barbapappa/issues/</a>
                </span>
                <span class="item">
                    @lang('pages.contact.newIssueMail'):
                    <a href="mailto:incoming+timvisee-barbapappa-4423731-issue-@incoming.gitlab.com">incoming+timvisee-barbapappa-4423731-issue-@incoming.gitlab.com</a>
                </span>
            </div>
        </div>
    </div>
@endsection
