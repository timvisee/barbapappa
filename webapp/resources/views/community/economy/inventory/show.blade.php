@extends('layouts.app')

@section('title', $inventory->name)
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.inventory.show', $inventory);
    $menusection = 'community_manage';

    use App\Http\Controllers\InventoryController;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.name')</td>
                <td>{{ $inventory->name }}</td>
            </tr>
            <tr>
                <td>@lang('pages.community.economy')</td>
                <td>
                    <a href="{{ route('community.economy.show', [
                                'communityId'=> $community->human_id,
                                'economyId' => $economy->id
                            ]) }}">
                        {{ $economy->name }}
                    </a>
                </td>
            </tr>
            <tr>
                <td>@lang('misc.createdAt')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $inventory->created_at])</td>
            </tr>
            @if($inventory->created_at != $inventory->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $inventory->updated_at])</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- TODO: show inventory item list --}}

    @if(perms(InventoryController::permsManage()))
        <p>
            <div class="ui buttons">
                <a href="{{ route('community.economy.inventory.edit', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'inventoryId' => $inventory->id,
                        ]) }}"
                        class="ui button secondary">
                    @lang('misc.edit')
                </a>
                <a href="{{ route('community.economy.inventory.delete', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'inventoryId' => $inventory->id,
                        ]) }}"
                        class="ui button negative">
                    @lang('misc.delete')
                </a>
            </div>
        </p>
    @endif

    <p>
        <a href="{{ route('community.economy.inventory.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
