@extends('layouts.app')

@section('title', __('pages.barMember.memberSettings'))
@php
    $menusection = 'bar';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <div class="ui hidden divider"></div>

    {!! Form::open(['action' => [
        'BarController@doEditMember',
        $bar->human_id,
    ], 'method' => 'PUT', 'class' => 'ui form']) !!}
        <div class="ui message">
            <div class="header">@lang('pages.barMembers.nickname')</div>
            <p>@lang('pages.barMembers.nicknameDescription')</p>
        </div>

        <div class="field {{ ErrorRenderer::hasError('nickname') ? 'error' : '' }}">
            {{ Form::label('nickname', __('pages.barMembers.nickname') . ':') }}
            {{ Form::text('nickname', $economy_member->nickname, ['placeholder' => '']) }}
            {{ ErrorRenderer::inline('nickname') }}
        </div>

        <div class="ui hidden divider"></div>

        <div class="ui message">
            <div class="header">@lang('pages.barMember.visibility')</div>
            <p>@lang('pages.barMember.visibilityDescription')</p>
        </div>

        <div class="inline field">
            <div class="ui toggle checkbox">
                {{ Form::checkbox('show_in_buy', true, $economy_member->show_in_buy, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('show_in_buy', __('pages.barMember.showInBuy') . ' (' . __('general.recommended') . ')') }}
            </div>
            {{ ErrorRenderer::inline('show_in_buy') }}
        </div>

        <div class="inline field">
            <div class="ui toggle checkbox">
                {{ Form::checkbox('show_in_kiosk', true, $economy_member->show_in_kiosk, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('show_in_kiosk', __('pages.barMember.showInKiosk') . ' (' . __('general.recommended') . ')') }}
            </div>
            {{ ErrorRenderer::inline('show_in_kiosk') }}
        </div>

        <div class="ui hidden divider"></div>

        <button class="ui button primary" type="submit">@lang('misc.saveChanges')</button>
        <a href="{{ route('bar.member', ['barId' => $bar->human_id]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>
    {!! Form::close() !!}
@endsection
