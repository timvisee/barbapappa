@extends('layouts.app')

@section('content')

    <h2 class="ui header">{{ $bar->name }}</h2>

    <p>@lang('pages.bar.joinQuestion')</p>

    {!! Form::open(['action' => ['BarController@doJoin', 'barId' => $bar->id], 'method' => 'POST', 'class' => 'ui form']) !!}
        <div class="ui buttons">
            <button class="ui button positive" type="submit">@lang('pages.bar.yesJoin')</button>
            <div class="or" data-text="@lang('general.or')"></div>
            <a href="{{ route('bar.show', ['barId' => $bar->id]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
        </div>
    {!! Form::close() !!}

@endsection
