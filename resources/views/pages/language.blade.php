@extends('layouts.app')

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
        <ul class="ui-listview" data-role="listview" data-inset="false">
            @foreach($selectableLocales as $locale)
                <li>
                    <a href="{{ route('language', ['locale' => $locale]) }}" class="ui-btn ui-btn-icon-right ui-icon-glyphicons ui-icon-glyphicons-chevron-right">
                        <span class="{{ langManager()->getLocaleFlagClass($locale, false, true) }} ui-li-icon" style="margin-right: 5px;"></span>
                        @lang('lang.name', [], $locale)
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <i>No languages available...</i>
    @endif

@endsection
