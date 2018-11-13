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
                <td>
                    @lang('pages.supportedCurrencies.title')
                    ({{ $currencies->count() }})
                </td>
                <td>
                    @if($currencies->isNotEmpty())
                        <div class="ui bulleted list">
                            @forelse($currencies as $currency)
                                <div class="item">
                                    <a href="{{ route('community.economy.currency.show', ['communityId' => $community->id, 'economyId' => $economy->id, 'supportedCurrencyId' => $currency->id]) }}">
                                        {{ $currency->displayName}}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p><i>None</i></p>
                    @endif

                    @if(perms(EconomyCurrencyController::permsView()))
                        <p>
                            <a href="{{ route('community.economy.currency.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                                    class="mini ui button basic">
                                @lang('misc.manage')
                            </a>
                        </p>
                    @endif
                </td>
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

        @if(perms(EconomyCurrencyController::permsView()))
            <div class="ui buttons">
                <a href="{{ route('community.economy.currency.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                        class="ui button">
                    @lang('misc.currencies')
                </a>
            </div>
        @endif
    </p>

    <p>
        <a href="{{ route('community.economy.index', ['communityId' => $community->human_id]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
