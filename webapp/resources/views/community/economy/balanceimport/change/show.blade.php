@extends('layouts.app')

@section('title', __('pages.balanceImportChange.change'))

@php
    use \App\Http\Controllers\BalanceImportChangeController;

    // Define menulinks
    $menulinks[] = [
        'name' => __('general.goBack'),
        'link' => route('community.economy.balanceimport.change.index', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'systemId' => $system->id,
            'eventId' => $event->id,
        ]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.user')</td>
                <td>
                    <div class="ui list">
                        <div class="item">{{ $change->alias->name }} ({{ $change->alias->email }})</div>
                    </div>
                </td>
            </tr>
            @if($change->balance != null)
                <tr>
                    <td>@lang('pages.balanceImportChange.finalBalance')</td>
                    <td>{!! $change->formatBalance(BALANCE_FORMAT_COLOR) !!}</td>
                </tr>
            @endif
            @if($change->cost != null)
                <tr>
                    <td>@lang('pages.balanceImportChange.cost')</td>
                    <td>{!! $change->formatCost(BALANCE_FORMAT_COLOR) !!}</td>
                </tr>
            @endif
            <tr>
                <td>@lang('misc.submitter')</td>
                <td>{{ $change->submitter->name }}</td>
            </tr>
            @if($change->accepted_at == null)
                <tr>
                    <td>@lang('misc.accepted')</td>
                    <td>@lang('general.no')</td>
                </tr>
            @else
                <tr>
                    <td>@lang('misc.acceptedBy')</td>
                    <td>
                        @if($change->accepter != null)
                            {{ $change->accepter->name }}
                        @else
                            <i>@lang('misc.unknownUser')</i>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('misc.acceptedAt')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $change->accepted_at])</td>
                </tr>
            @endif
            @if($change->committed_at == null)
                <tr>
                    <td>@lang('misc.committed')</td>
                    <td>@lang('general.no')</td>
                </tr>
            @else
                <tr>
                    <td>@lang('misc.committedAt')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $change->committed])</td>
                </tr>
            @endif
            <tr>
                <td>@lang('misc.createdAt')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $change->created_at])</td>
            </tr>
            @if($change->created_at != $change->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $change->updated_at])</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if($change->mutation_id != null)
        <p>
            <div class="ui buttons">
                <a href="{{ route('transaction.mutation.show', [
                            'transactionId' => $community->mutation->transaction_id,
                            'mutationId' => $change->mutation_id,
                        ]) }}"
                        class="ui button basic">
                    @lang('pages.mutations.viewMutation')
                </a>
            </div>
        </p>
    @endif

    @if(perms(BalanceImportChangeController::permsManage()))
        <p>
            <a href="{{ route('community.economy.balanceimport.change.delete', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                        'systemId' => $system->id,
                        'eventId' => $event->id,
                        'changeId' => $change->id,
                    ]) }}"
                    class="ui button negative">
                @lang('misc.delete')
            </a>
        </p>
    @endif

    <p>
        <a href="{{ route('community.economy.balanceimport.change.index', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'systemId' => $system->id,
            'eventId' => $event->id,
        ]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
