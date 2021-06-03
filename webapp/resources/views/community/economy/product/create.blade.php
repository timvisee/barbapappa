@extends('layouts.app')

@section('title', __('pages.products.' . ($clone ? 'clone' : 'new') . 'Product'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open([
        'action' => [
            'ProductController@doCreate',
            $community->human_id,
            $economy->id,
        ],
        'method' => 'POST',
        'class' => 'ui form'
    ]) !!}
        <div class="required field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', $clone ? $cloneProduct->name : '', ['placeholder' => __('pages.products.namePlaceholder')]) }}
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
                    $value = $clone ?
                        $cloneProduct
                            ->names
                            ->whereStrict('locale', $locale)
                            ->map(function($p) { return $p->name; })
                            ->first()
                        : null;
                @endphp
                <div class="field {{ ErrorRenderer::hasError($field) ? 'error' : '' }}">
                    {{ Form::label($field, __('lang.name', [], $locale) . ':') }}
                    <div class="ui labeled input">
                        {{ Form::text(
                            $field,
                            e($value),
                            ['id' => $field, 'placeholder' => __('pages.products.namePlaceholder', [], $locale)]
                        ) }}
                    </div>
                    {{ ErrorRenderer::inline($field) }}
                </div>
            @endforeach
        </div>

        <div class="ui divider"></div>

        <div class="field {{ ErrorRenderer::hasError('tags') ? 'error' : '' }}">
            {{ Form::label('tags', __('misc.tags') . ':') }}
            {{ Form::text('tags', $clone ? $cloneProduct->tags : '', ['placeholder' => __('pages.products.tagsPlaceholder')]) }}
            {{ ErrorRenderer::inline('tags') }}
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
                        $value = $clone ? $cloneProduct
                                ->prices
                                ->whereStrict('currency_id', $currency->id)
                                ->map(function($p) { return $p->price; })
                                ->first()
                            : null;
                    @endphp
                    <div class="field {{ ErrorRenderer::hasError($field) ? 'error' : '' }}">
                        {{ Form::label($field, $currency->name . ':') }}
                        <div class="ui labeled input">
                            {{ Form::label($field, $currency->symbol, ['class' => 'ui label']) }}
                            {{ Form::text(
                                $field,
                                e($value),
                                ['id' => $field, 'placeholder' => '1.23']
                            ) }}
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
                {{ Form::checkbox('enabled', true, !$clone || $cloneProduct->enabled, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('enabled', __('pages.products.enabledDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('enabled') }}
        </div>

        <br />

        <div class="ui buttons">
            <button class="ui button primary" type="submit" name="submit" value="">
                @lang('misc.add')
            </button>
            <button class="ui button primary basic" type="submit" name="submit" value="clone">
                @lang('misc.addAndClone')
            </button>
        </div>
        <a href="{{ route('community.economy.product.index', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
        ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
