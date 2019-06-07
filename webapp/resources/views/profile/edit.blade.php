@extends('layouts.app')

@section('title', __('pages.editProfile.name'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open(['action' => ['ProfileController@update', $user->id], 'method' => 'PUT', 'class' => 'ui form']) !!}

        <div class="two fields">
            <div class="field {{ ErrorRenderer::hasError('first_name') ? 'error' : '' }}">
                {{ Form::label('first_name', __('account.firstName') . ':') }}
                {{ Form::text('first_name', $user->first_name, ['placeholder' => __('account.firstNamePlaceholder')]) }}
                {{ ErrorRenderer::inline('first_name') }}
            </div>

            <div class="field {{ ErrorRenderer::hasError('last_name') ? 'error' : '' }}">
                {{ Form::label('last_name', __('account.lastName') . ':') }}
                {{ Form::text('last_name', $user->last_name, ['placeholder' => __('account.lastNamePlaceholder')]) }}
                {{ ErrorRenderer::inline('last_name') }}
            </div>
        </div>

        @php
            // Create a locales map for the selection box
            $locales = [];
            foreach(langManager()->getLocales(true, false) as $entry)
                $locales[$entry] = __('lang.name', [], $entry);
        @endphp

        <div class="field {{ ErrorRenderer::hasError('language') ? 'error' : '' }}">
            {{ Form::label('language', __('lang.language')) }}

            <div class="ui fluid selection dropdown">
                {{ Form::hidden('language', langManager()->getLocale()) }}
                <i class="dropdown icon"></i>

                <div class="default text">@lang('misc.unspecified')</div>
                <div class="menu">
                    @foreach($locales as $locale => $name)
                        <div class="item" data-value="{{ $locale }}">
                            <span class="{{ langManager()->getLocaleFlagClass($locale, false, true) }} flag"></span>
                            {{ $name }}
                        </div>
                    @endforeach
                </div>
            </div>

            {{ ErrorRenderer::inline('language') }}
        </div>

        <button class="ui button primary" type="submit">@lang('misc.saveChanges')</button>
        <a href="{{ route('account', ['userId' => $user->id]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
