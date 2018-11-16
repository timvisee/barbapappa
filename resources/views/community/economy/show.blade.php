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

    <p>
        @if(perms(EconomyController::permsManage()))
            <div class="ui buttons">
                <a href="{{ route('community.economy.edit', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                        class="ui button secondary">
                    @lang('misc.edit')
                </a>
                <a href="{{ route('community.economy.delete', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                        class="ui button negative">
                    @lang('misc.delete')
                </a>
            </div>
        @endif
    </p>

    @if(perms(EconomyCurrencyController::permsView()))
        <div class="ui divider hidden"></div>

        <div class="ui top attached vertical menu fluid">
            <h5 class="ui item header">
                @lang('misc.currencies')
                ({{ $currencies->count() }})
            </h5>
            @forelse($currencies as $currency)
                <a class="item"
                        href="{{ route('community.economy.currency.show', [
                            'communityId' => $community->id,
                            'economyId' => $economy->id,
                            'economyCurrencyId' => $currency->id
                        ]) }}">
                    {{ $currency->displayName}}
                </a>
            @endforeach
        </div>
        <a href="{{ route('community.economy.currency.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui bottom attached button">
            @lang('misc.manage')
        </a>

        <div class="ui divider hidden"></div>
    @endif

    <p>
        <a href="{{ route('community.economy.index', ['communityId' => $community->human_id]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
