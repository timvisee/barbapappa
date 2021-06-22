@extends('layouts.app')

@section('title', $bar->name)
@php
    $menusection = 'bar';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.bar.joinQuestion')</p>

    @php
        $user = barauth()->getSessionUser();
        $needsCode = $bar->needsPassword($user);
        $code = Request::query('code');

        // Whether to show the code field, we simplify the join view if not showing
        $showCode = $needsCode && (empty($code) || ErrorRenderer::hasError('code'));
    @endphp

    @if($showCode)
        <div class="ui hidden divider"></div>

        <div class="ui warning message visible">
            <div class="header">@lang('misc.protected')</div>
            <p>@lang('pages.bar.protectedByCode')</p>
        </div>
    @endif

    {!! Form::open(['action' => ['BarController@doJoin', 'barId' => $bar->human_id], 'method' => 'POST', 'class' => 'ui form']) !!}

        @if($showCode)
            <div class="required field {{ ErrorRenderer::hasError('code') ? 'error' : '' }}">
                {{ Form::label('code', __('misc.code') . ':') }}
                {{ Form::text('code', $code, ['placeholder' => __('misc.codePlaceholder')]) }}
                {{ ErrorRenderer::inline('code') }}
            </div>
        @elseif($needsCode)
            {{ Form::hidden('code', $code) }}
        @endif

        <div class="ui divider hidden"></div>

        @if($showCode)
            <div class="ui buttons">
                <button class="ui button positive" type="submit">@lang('pages.bar.yesJoin')</button>
                <div class="or" data-text="@lang('general.or')"></div>
                <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}"
                        class="ui button negative">
                    @lang('general.noGoBack')
                </a>
            </div>
        @else
            <p>
                <button class="ui large button positive" type="submit">@lang('pages.bar.yesJoin')</button>
                @lang('general.or')
                <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}"
                        class="subtle link">
                    @lang('general.noGoBack')
                </a>
            </p>
        @endif

    {!! Form::close() !!}
@endsection
