@extends('layouts.app')

@section('title', $system->name)
@php
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.balanceImport.deleteQuestion')</p>

    {!! Form::open([
        'action' => [
            'BalanceImportSystemController@doDelete',
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'systemId' => $system->id,
        ],
        'method' => 'DELETE',
        'class' => 'ui form'
    ]) !!}
        <div class="ui top attached warning message visible">
            <span class="halflings halflings-warning-sign"></span>
            @lang('misc.cannotBeUndone')
        </div>

        {{-- Delete confirmation checkbox --}}
        <div class="ui bottom attached segment">
            <div class="required field {{ ErrorRenderer::hasError('confirm_delete') ? 'error' : '' }}">
                <div class="ui checkbox">
                    {{ Form::checkbox('confirm_delete', true, false, ['tabindex' => 0, 'class' => 'hidden']) }}
                    {{ Form::label('confirm_delete', __('misc.iUnderstandDelete')) }}
                </div>
                <br />
                {{ ErrorRenderer::inline('confirm_delete') }}
            </div>
        </div>

        <br />

        <div class="ui buttons">
            <a href="{{ route('community.economy.balanceimport.show', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                        'systemId' => $system->id,
                    ]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesRemove')</button>
        </div>
    {!! Form::close() !!}
@endsection
