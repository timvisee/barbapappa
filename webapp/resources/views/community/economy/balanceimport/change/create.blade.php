@extends('layouts.app')

@section('title', __('pages.balanceImportChange.newChange'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <div class="ui two item menu">
        <a href="{{ route('community.economy.balanceimport.change.create', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'systemId' => $system->id,
                'eventId' => $event->id,
                ]) }}"
                class="item active">
            @lang('misc.add')
        </a>
        <a href="{{ route('community.economy.balanceimport.change.importJson', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'systemId' => $system->id,
                'eventId' => $event->id,
                ]) }}"
                class="item">
            @lang('misc.import')
        </a>
    </div>

    <div class="ui hidden divider"></div>

    {!! Form::open([
        'action' => [
            'BalanceImportChangeController@doCreate',
            $community->human_id,
            $economy->id,
            $system->id,
            $event->id,
        ],
        'method' => 'POST',
        'class' => 'ui form'
    ]) !!}

        <p>@lang('pages.balanceImportChange.enterAliasNameEmail')</p>

        <div class="ui hidden divider"></div>

        <div class="field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ' (' .  __('general.optional') . '):') }}
            {{ Form::text('name', '', ['placeholder' => __('account.firstNamePlaceholder') . ' ' .  __('account.lastNamePlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <div class="field {{ ErrorRenderer::hasError('email') ? 'error' : '' }}">
            {{ Form::label('email', __('account.email') . ':') }}
            {{ Form::text('email', '', ['placeholder' => __('account.emailPlaceholder')]) }}
            {{ ErrorRenderer::inline('email') }}
        </div>

        <div class="ui section divider"></div>

        <p>@lang('pages.balanceImportChange.balanceOrCostDescription')</p>

        <div class="three fields">
            <div class="field {{ ErrorRenderer::hasError('currency') ? 'error' : '' }}">
                {{ Form::label('currency', __('misc.currency') . ':') }}

                <div class="ui fluid selection dropdown">
                    {{ Form::hidden('currency', $currencies->first()->id) }}
                    <i class="dropdown icon"></i>

                    <div class="default text">@lang('misc.pleaseSpecify')</div>
                    <div class="menu">
                        @foreach($currencies as $c)
                            <div class="item" data-value="{{ $c->id }}">{{ $c->displayName }}</div>
                        @endforeach
                    </div>
                </div>

                {{ ErrorRenderer::inline('currency') }}
            </div>

            <div class="field {{ ErrorRenderer::hasError('balance') ? 'error' : '' }}">
                {{ Form::label('balance', __('pages.balanceImportChange.finalBalance') . ':') }}
                {{ Form::text('balance', '', ['id' => 'balance', 'placeholder' => '1.23']) }}
                {{ ErrorRenderer::inline('balance') }}
            </div>

            <div class="field {{ ErrorRenderer::hasError('cost') ? 'error' : '' }}">
                {{ Form::label('cost', __('pages.balanceImportChange.cost') . ':') }}
                {{ Form::text('cost', '', ['id' => 'cost', 'placeholder' => '1.23']) }}
                {{ ErrorRenderer::inline('cost') }}
            </div>
        </div>

        <div class="ui hidden divider"></div>

        <button class="ui button primary" type="submit" name="submit" value="">
            @lang('misc.add')
        </button>
        <a href="{{ route('community.economy.balanceimport.change.index', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'systemId' => $system->id,
                    'eventId' => $event->id,
                ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
