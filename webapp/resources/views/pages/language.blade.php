@extends('layouts.app')

@section('title', 'lang.language')

@section('content')
    <?php
        // Get the title and selectable locales, and build a description string
        $titleLocales = array_slice(langManager()->getLocales(false, false), 0, 2);
        $selectableLocales = langManager()->getLocales(true, false);
    ?>

    {{-- Create a properly styled element here --}}
    <div class="highlight-box">
        <i class="glyphicons glyphicons-flag attention-icon"></i>
        <br />

        @foreach($titleLocales as $locale)
            <p>
                <i>@lang('lang.choose', [], $locale)...</i>
            </p>
        @endforeach
    </div>

    @if(count($selectableLocales) > 0)
        <div class="ui stackable two column grid">
            @foreach($selectableLocales as $locale)
                <div class="column">
                    <a href="{{ route('language', ['locale' => $locale]) }}" class="ui button fluid labeled icon">
                        <i class="icon">
                            <span class="{{ langManager()->getLocaleFlagClass($locale, false, true) }}"></span>
                        </i>
                        @lang('lang.name', [], $locale)
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <i>No languages available...</i>
    @endif
@endsection
