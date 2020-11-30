@extends('layouts.app')

@section('title', __('pages.balanceImportChange.importJsonChanges'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <div class="ui two item menu">
        <a href="{{ route('community.economy.balanceimport.change.create', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'systemId' => $system->id,
                'eventId' => $event->id,
                ]) }}"
                class="item">
            @lang('misc.add')
        </a>
        <a href="{{ route('community.economy.balanceimport.change.importJson', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'systemId' => $system->id,
                'eventId' => $event->id,
                ]) }}"
                class="item active">
            @lang('misc.import')
        </a>
    </div>

    <div class="ui hidden divider"></div>

    {!! Form::open([
        'action' => [
            'BalanceImportChangeController@doImportJson',
            $community->human_id,
            $economy->id,
            $system->id,
            $event->id,
        ],
        'method' => 'POST',
        'class' => 'ui form'
    ]) !!}

        <p>@lang('pages.balanceImportChange.importJsonDescription')</p>

        <div class="ui divider hidden"></div>

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

        <div class="ui section divider"></div>

        <p>@lang('pages.balanceImportChange.importJsonFieldsDescription')</p>

        <div class="three fields">
            <div class="field">
                {{ Form::label('field_name', __('misc.name') . ':') }}
                {{ Form::text('field_name', 'name', ['id' => 'field_name', 'disabled' => 'disabled']) }}
                {{ ErrorRenderer::inline('field_name') }}
            </div>

            <div class="field">
                {{ Form::label('field_email', __('account.email') . ':') }}
                {{ Form::text('field_email', 'email', ['id' => 'field_email', 'disabled' => 'disabled']) }}
                {{ ErrorRenderer::inline('field_email') }}
            </div>

            <div class="field">
                {{ Form::label('field_balance', __('pages.balanceImportChange.finalBalance') . ':') }}
                {{ Form::text('field_balance', 'balance_new', ['id' => 'field_balance', 'disabled' => 'disabled']) }}
                {{ ErrorRenderer::inline('field_balance') }}
            </div>
        </div>

        <div class="ui section divider"></div>

        <p>@lang('pages.balanceImportChange.importJsonDataDescription')</p>

        <div class="field {{ ErrorRenderer::hasError('data') ? 'error' : '' }}">
            {{ Form::label('data', __('pages.balanceImportChange.jsonData') . ':') }}
            {{ Form::textarea('data', '', [
                'id' => 'data',
                'rows' => 3,
                'placeholder' => json_encode([
                    [
                        "name" => __('account.firstNamePlaceholder') . ' '  . __('account.lastNamePlaceholder'),
                        "email" => __('account.emailPlaceholder'),
                        "balance_new" => "1.23",
                    ],
                ]),
            ]) }}
            {{ ErrorRenderer::inline('data') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('allow_duplicate') ?  'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('allow_duplicate', true, false, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('allow_duplicate', __('pages.balanceImportAlias.allowAddingSameUserMultiple')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('allow_duplicate') }}
        </div>

        <div class="ui divider hidden"></div>

        <button class="ui button primary" type="submit" name="submit" value="">
            @lang('misc.importAll')
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
