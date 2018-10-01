@extends('layouts.app')

@section('content')

    <h2 class="ui header">{{ $email->email }}</h2>

    <p>@lang('pages.accountPage.email.deleteQuestion')</p>

    {!! Form::open(['action' => ['EmailController@doDelete', 'userId' => $user->id, 'emailId' => $email->id], 'method' => 'DELETE', 'class' => 'ui form']) !!}
        <div class="ui buttons">
            <a href="{{ route('account.emails', ['userId' => $user->id]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesDelete')</button>
        </div>
    {!! Form::close() !!}

@endsection
