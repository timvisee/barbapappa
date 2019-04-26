@extends('layouts.app')

@section('title', __('pages.contact'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>
        The contact page has not been implemented yet.
    </p>

    <p>
        For support, you may send a message to the following email address.<br />
        Or open an issue on the issues page for bugs or development ideas.
    </p>

    <div class="ui bulleted list">
        <span class="item">
            Email:
            <a href="mailto:incoming+timvisee-barbapappa-4423731-issue-@incoming.gitlab.com">incoming+timvisee-barbapappa-4423731-issue-@incoming.gitlab.com</a>
        </span>
        <span class="item">
            Issues:
            <a href="https://gitlab.com/timvisee/barbapappa/issues/">https://gitlab.com/timvisee/barbapappa/issues/</a>
        </span>
    </div>
@endsection
