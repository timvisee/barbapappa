@extends('layouts.app')

@section('title', $system->name)
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.balanceimport.show', $system);
    $menusection = 'community_manage';

    use App\Http\Controllers\BalanceImportSystemController;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.name')</td>
                <td>
                    <div class="ui list">
                        <div class="item">{{ $system->name }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>@lang('misc.createdAt')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $system->created_at])</td>
            </tr>
            @if($system->created_at != $system->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $system->updated_at])</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if(perms(BalanceImportSystemController::permsManage()))
        <p>
            <div class="ui buttons">
                <a href="{{ route('community.economy.balanceimport.edit', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'systemId' => $system->id,
                        ]) }}"
                        class="ui button secondary">
                    @lang('misc.edit')
                </a>
                <a href="{{ route('community.economy.balanceimport.delete', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'systemId' => $system->id,
                        ]) }}"
                        class="ui button negative">
                    @lang('misc.delete')
                </a>
            </div>

            <a href="{{ route('community.economy.balanceimport.event.index', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                        'systemId' => $system->id,
                    ]) }}"
                    class="ui button basic">
                @lang('pages.balanceImportEvent.events')
            </a>
        </p>
    @endif

    <p>
        <a href="{{ route('community.economy.balanceimport.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
