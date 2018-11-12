@extends('layouts.app')

@php
    use \App\Http\Controllers\EconomyController;
    use \App\Http\Controllers\EconomyCurrencyController;
@endphp

@section('content')
    <h2 class="ui header">{{ $economy->name }}</h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.name')</td>
                <td>{{ $economy->name }}</td>
            </tr>
            <tr>
                <td>@lang('misc.createdAt')</td>
                <td>{{ $economy->created_at }}</td>
            </tr>
            @if($economy->created_at != $economy->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>{{ $economy->updated_at }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if(perms(EconomyCurrencyController::permsView()))
        <p>
            <a href="{{ route('community.economy.currency.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                    class="ui button basic">
                {{-- TODO: proper translation --}}
                {{-- @lang('misc.edit') --}}
                Currencies
            </a>
        </p>
    @endif

    @if(perms(EconomyController::permsManage()))
        <p>
            <a href="{{ route('community.economy.edit', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                    class="ui button basic secondary">
                @lang('misc.edit')
            </a>
            <a href="{{ route('community.economy.delete', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                    class="ui button basic negative">
                @lang('misc.delete')
            </a>
        </p>
    @endif

    <a href="{{ route('community.economy.index', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('general.goBack')
    </a>
@endsection
