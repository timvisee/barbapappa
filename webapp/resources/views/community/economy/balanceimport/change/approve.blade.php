@extends('layouts.app')

@section('title', __('pages.balanceImportChange.approveChange'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.balanceImportChange.approveQuestion')</p>

    {!! Form::open([
        'action' => [
            'BalanceImportChangeController@doApprove',
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'systemId' => $system->id,
            'eventId' => $event->id,
            'changeId' => $change->id,
        ],
        'method' => 'PUT',
        'class' => 'ui form'
    ]) !!}
        <div class="ui warning message visible">
            <span class="halflings halflings-warning-sign"></span>
            @lang('misc.cannotBeUndone')
        </div>

        <br />

        <div class="ui buttons">
            <a href="{{ route('community.economy.balanceimport.change.show', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                        'systemID' => $system->id,
                        'eventId' => $event->id,
                        'changeId' => $change->id,
                    ]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesApprove')</button>
        </div>
    {!! Form::close() !!}
@endsection
