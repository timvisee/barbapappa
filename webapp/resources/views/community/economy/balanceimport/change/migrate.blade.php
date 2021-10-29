@extends('layouts.app')

@section('title', __('pages.balanceImportChange.migrateAlias'))
@php
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.balanceImportChange.migrateDescription')</p>

    {!! Form::open([
        'action' => [
            'BalanceImportChangeController@doMigrate',
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'systemId' => $system->id,
            'eventId' => $event->id,
            'changeId' => $change->id,
        ],
        'method' => 'PUT',
        'class' => 'ui form'
    ]) !!}

        <div class="required field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', $alias->name, ['placeholder' => __('account.firstNamePlaceholder') . ' ' .  __('account.lastNamePlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <div class="required field {{ ErrorRenderer::hasError('email') ? 'error' : '' }}">
            {{ Form::label('email', __('account.email') . ':') }}
            {{ Form::text('email', $alias->email, ['type' => 'email', 'placeholder' => __('account.emailPlaceholder')]) }}
            {{ ErrorRenderer::inline('email') }}
        </div>

        <div class="ui divider hidden"></div>

        <button class="ui button primary" type="submit" name="submit" value="">
            @lang('misc.migrate')
        </button>
        <a href="{{ route('community.economy.balanceimport.change.show', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'systemId' => $system->id,
                    'eventId' => $event->id,
                    'changeId' => $change->id,
                ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>
    {!! Form::close() !!}
@endsection
