@extends('layouts.app')

@section('title', __('pages.balanceImportChange.change'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.balanceimport.change.show', $change);
@endphp

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
            @if(!$change->isApproved())
                <tr>
                    <td>@lang('misc.approved')</td>
                    <td>@lang('general.no')</td>
                </tr>
            @else
                <tr>
                    <td>@lang('misc.approvedBy')</td>
                    <td>
                        @if($change->approver != null)
                            {{ $change->approver->name }}
                        @else
                            <i>@lang('misc.unknownUser')</i>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('misc.approvedAt')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $change->approved_at])</td>
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
                    <td>@include('includes.humanTimeDiff', ['time' => $change->committed_at])</td>
                </tr>
            @endif
            @if($change->mutation_id != null)
                <tr>
                    <td>@lang('misc.deposited')</td>
                    <td>{!!  $change->mutation->formatAmount(BALANCE_FORMAT_COLOR) !!}</td>
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

    <p>
        @if(perms(BalanceImportChangeController::permsManage()))
            <div class="ui buttons">
                @if(!$change->isApproved())
                    <a href="{{ route('community.economy.balanceimport.change.approve', [
                                'communityId' => $community->human_id,
                                'economyId' => $economy->id,
                                'systemId' => $system->id,
                                'eventId' => $event->id,
                                'changeId' => $change->id,
                            ]) }}"
                            class="ui button positive">
                        @lang('misc.approve')
                    </a>

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
                @else
                    <a href="{{ route('community.economy.balanceimport.change.undo', [
                                'communityId' => $community->human_id,
                                'economyId' => $economy->id,
                                'systemId' => $system->id,
                                'eventId' => $event->id,
                                'changeId' => $change->id,
                            ]) }}"
                            class="ui button basic negative">
                        @lang('misc.undo')
                    </a>
                @endif
            </div>
        @endif

        @if($change->mutation_id != null)
            <a href="{{ route('transaction.mutation.show', [
                        'transactionId' => $change->mutation->transaction_id,
                        'mutationId' => $change->mutation_id,
                    ]) }}"
                    class="ui button basic">
                @lang('pages.mutations.viewMutation')
            </a>
        @endif
    </p>

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
