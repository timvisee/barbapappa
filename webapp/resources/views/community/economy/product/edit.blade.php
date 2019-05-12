@extends('layouts.app')

@section('title', __('pages.products.editProduct'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open([
        'action' => [
            'ProductController@doEdit',
            $community->human_id,
            $economy->id,
            $product->id,
        ],
        'method' => 'PUT',
        'class' => 'ui form'
    ]) !!}
        <div class="field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', $product->name, ['placeholder' => __('pages.products.namePlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <div class="ui divider"></div>

        <div class="ui message">
            <div class="header">@lang('pages.products.localizedNames')</div>
            <p>@lang('pages.products.localizedNamesDescription')</p>
        </div>

        <div class="three fields">
            @foreach($locales as $locale)
                @php
                    $field = 'name_' . $locale;
                    $value = $product
                        ->names
                        ->whereStrict('locale', $locale)
                        ->map(function($p) { return $p->name; })
                        ->first();
                @endphp
                <div class="field {{ ErrorRenderer::hasError($field) ? 'error' : '' }}">
                    <label>@lang('lang.name', [], $locale) ({{ __('general.optional') }}):</label>
                    <div class="ui labeled input">
                        <input type="text"
                        placeholder="@lang('pages.products.namePlaceholder', [], $locale)"
                        id="{{ $field }}" name="{{ $field }}" value="{{ $value }}" />
                    </div>
                    {{ ErrorRenderer::inline($field) }}
                </div>
            @endforeach
        </div>

        <div class="ui divider"></div>

        <div class="ui message">
            <div class="header">@lang('pages.products.prices')</div>
            <p>@lang('pages.products.pricesDescription')</p>
        </div>

        @if($economy->currencies->isNotEmpty())
            <div class="six fields">
                @foreach($economy->currencies as $currency)
                    @php
                        $field = 'price_' . $currency->id;
                        $value = $product
                            ->prices
                            ->whereStrict('currency_id', $currency->id)
                            ->map(function($p) { return $p->price; })
                            ->first();
                    @endphp
                    <div class="field {{ ErrorRenderer::hasError($field) ? 'error' : '' }}">
                        <label>{{ $currency->name }} ({{ __('general.optional') }}):</label>
                        <div class="ui labeled input">
                            <label for="{{ $field }}" class="ui label">{{ $currency->symbol }}</label>
                            <input type="text" placeholder="1.23" id="{{ $field }}" name="{{ $field }}" value="{{ $value }}" />
                        </div>
                        {{ ErrorRenderer::inline($field) }}
                    </div>
                @endforeach
            </div>
        @else
            <p><i>@lang('pages.currencies.noCurrencies')</i></p>
        @endif

        <a href="{{ route('community.economy.currency.index', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
        ]) }}">
            @lang('pages.currencies.manage')
        </a>

        <div class="ui divider"></div>

        <div class="inline field {{ ErrorRenderer::hasError('enabled') ? 'error' : '' }}">
            <div class="ui checkbox">
                <input type="checkbox"
                        name="enabled"
                        tabindex="0"
                        class="hidden"
                        {{ $product->enabled ? 'checked="checked"' : '' }}>
                {{ Form::label('enabled', __('pages.products.enabledDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('enabled') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('archived') ? 'error' : '' }}">
            <div class="ui checkbox">
                <input type="checkbox"
                        name="archived"
                        tabindex="0"
                        class="hidden"
                        {{ $product->archived ? 'checked="checked"' : '' }}>
                {{ Form::label('archived', __('pages.products.archivedDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('archived') }}
        </div>

        <br />

        <button class="ui button primary" type="submit">@lang('misc.saveChanges')</button>
        <a href="{{ route('community.economy.product.show', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'productId' => $product->id,
        ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
