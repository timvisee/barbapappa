@extends('layouts.app')

@section('title', __('pages.products.createProduct'))

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
        <div class="field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', '', ['placeholder' => __('pages.products.namePlaceholder')]) }}
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
                        checked="checked">
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
                        class="hidden">
                {{ Form::label('archived', __('pages.products.archivedDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('archived') }}
        </div>

        <br />

        <button class="ui button primary" type="submit">@lang('misc.add')</button>
        <a href="{{ route('community.economy.product.index', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
        ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
