@extends('layouts.app')

@section('content')
    <?php
        // Get the title locales and selectable locales
        $titleLocales = array_slice(langManager()->getLocales(false, false), 0, 2);
        $selectableLocales = langManager()->getLocales(true, false);

        // Build the title and description
        $title = implode(' / ', array_map(function($locale) {
            return __('lang.language', [], $locale);
        }, $titleLocales));
        $description = implode(' / ', array_map(function($locale) {
            return __('lang.choose', [], $locale);
        }, $titleLocales));
    ?>

    <h1>{{ $title }}</h1>
    <p>{{ $description }}:</p>

    @if(count($selectableLocales) > 0)
        <ul>
            @foreach($selectableLocales as $locale)
                <li>
                    <img src="{{ langManager()->getLocaleFlagUrl($locale) }}"
                        alt="{{ __('lang.nameFlag', [], $locale) }}"
                        title="{{ __('lang.nameFlag', [], $locale) }}" />

                    <a href="{{ route('language', ['locale' => $locale]) }}">
                        @lang('lang.name', [], $locale)
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <i>No languages available...</i>
    @endif

@endsection
