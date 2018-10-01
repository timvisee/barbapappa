@extends('layouts.app')

@section('content')
    <h2 class="ui header">@lang('pages.privacy.title')</h2>
    <p>@lang('pages.privacy.description')</p>

    <p><i>@lang('pages.privacy.onlyEnglishNote')</i></p>

    <div class="ui piled segment">
        <h3 class="ui header">Privacy Policy</h3>
        <p>
            Your privacy is important to us. It is BARbapAPPa's policy to respect
            your privacy regarding any information we may collect from you across
            our website, <a href="{{ URL::to('/') }}">{{ URL::to('/') }}</a>, and
            other sites we own and operate.
        </p>

        <p>
            We only ask for personal information when we truly need it to provide a
            service to you. We collect it by fair and lawful means, with your
            knowledge and consent. We also let you know why we’re collecting it and
            how it will be used.
        </p>

        <p>
            We only retain collected information for as long as necessary to
            provide you with your requested service. What data we store, we’ll
            protect within commercially acceptable means to prevent loss and
            theft, as well as unauthorised access, disclosure, copying, use or
            modification.
        </p>

        <p>
            We don’t share any personally identifying information publicly or
            with third-parties, except when required to by law.
        </p>

        <p>
            Our website may link to external sites that are not operated by us.
            Please be aware that we have no control over the content and practices
            of these sites, and cannot accept responsibility or liability for
            their respective privacy policies.
        </p>

        <p>
            You are free to refuse our request for your personal information, with
            the understanding that we may be unable to provide you with some of
            your desired services.
        </p>

        <p>
            Your continued use of our website will be regarded as acceptance of our
            practices around privacy and personal information. If you have any
            questions about how we handle user data and personal information, feel
            free to contact us.
        </p>

        <p>This policy is effective as of 1 October 2018.</p>

        {{-- Generated with: https://getterms.io/g/?url=https%3A%2F%2Fbar.visee.me%2F&name=BARbapAPPa&location=The%20Netherlands&effective_date=1%20October%202018 --}}
    </div>

    <h3 class="ui header">@lang('pages.privacy.questions')</h3>
    <p>@lang('pages.privacy.questionsDescription')</p>
    <a href="{{ route('contact') }}" class="ui button basic">@lang('pages.contactUs')</a>
@endsection
