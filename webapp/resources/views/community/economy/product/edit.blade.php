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

        {{-- TODO: pricing --}}
        {{-- <div class="ui divider"></div> --}}

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
