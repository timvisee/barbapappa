@extends('layouts.app')

@section('title', __('pages.bar.generatePoster'))

@section('content')
    <h2 class="ui header">
        @yield('title')

        <div class="sub header">
            @lang('misc.for')
            <a href="{{ route('bar.manage', ['barId' => $bar->human_id]) }}">
                {{ $bar->name }}
            </a>
        </div>
    </h2>
    <p>@lang('pages.bar.generatePosterDescription', ['app' => config('app.name')])</p>

    {!! Form::open(['action' => ['BarController@doGeneratePoster', 'barId' => $bar->human_id], 'method' => 'POST', 'class' => 'ui form', 'target' => '_blank']) !!}

        @php
            // Create a locales map for the selection box
            $locales = [];
            foreach(langManager()->getLocales(true, false) as $entry)
                $locales[$entry] = __('lang.name', [], $entry);
        @endphp

        <div class="field {{ ErrorRenderer::hasError('language') ? 'error' : '' }}">
            {{ Form::label('language', __('lang.language')) }}

            <div class="ui fluid selection dropdown">
                <input type="hidden" name="language" value="{{ langManager()->getLocale() }}">
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

        <div class="inline field {{ ErrorRenderer::hasError('show_code') ?  'error' : '' }} {{ empty($bar->password) ? 'disabled' : '' }}">
            <div class="ui toggle checkbox">
                <input type="checkbox"
                        name="show_code"
                        tabindex="0"
                        class="hidden"
                        {{ !empty($bar->password) ? 'checked="checked"' : '' }}>
                {{ Form::label('show_code', __('pages.bar.showCodeOnPoster')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('show_code') }}
        </div>

        <br />

        <button class="ui button primary" type="submit">@lang('misc.download')</button>
        <a href="{{ route('bar.manage', ['barId' => $bar->human_id]) }}"
                class="ui button basic">
            @lang('pages.bar.backToBar')
        </a>

    {!! Form::close() !!}
@endsection
