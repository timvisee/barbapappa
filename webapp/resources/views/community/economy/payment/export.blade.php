@extends('layouts.app')

@section('title', __('pages.economyPayments.exportTitle'))
@php
    $menusection = 'community_manage';

    use App\Http\Controllers\EconomyPaymentController;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>@lang('pages.economyPayments.exportDescription')</p>

    <div class="ui hidden divider"></div>

    {!! Form::open([
        'action' => [
            'EconomyPaymentController@doExport',
            $community->human_id,
            $economy->id,
        ],
        'method' => 'POST',
        'class' => 'ui form'
    ]) !!}
        <div class="inline field {{ ErrorRenderer::hasError('headers') ?  'error' : '' }}">
            <div class="ui toggle checkbox">
                {{ Form::checkbox('headers', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('headers', __('misc.includeHeaders')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('headers') }}
        </div>

        <div class="two fields">
            <div class="field {{ ErrorRenderer::hasError('date_from') ? 'error' : '' }}">
                {{ Form::label('date_from', __('misc.fromDate') . ':') }}
                {{ Form::date('date_from', $firstDate, ['min' => $firstDate, 'max' => $lastDate]) }}
                {{ ErrorRenderer::inline('date_from') }}
            </div>

            <div class="field {{ ErrorRenderer::hasError('date_to') ? 'error' : '' }}">
                {{ Form::label('date_to', __('misc.toDate') . ' (' . lcfirst(__('misc.inclusive')) . '):') }}
                {{ Form::date('date_to', today(), ['min' => $firstDate, 'max' => $lastDate]) }}
                {{ ErrorRenderer::inline('date_to') }}
            </div>
        </div>

        <div class="field {{ ErrorRenderer::hasError('format') ? 'error' : '' }}">
            {{ Form::label('format', __('misc.format') . ':') }}

            <div class="ui fluid selection dropdown">
                {{ Form::hidden('format', config('bar.spreadsheet_export_types')[0]['type']) }}
                <i class="dropdown icon"></i>

                <div class="default text">@lang('misc.pleaseSpecify')</div>
                <div class="menu">
                    @foreach(config('bar.spreadsheet_export_types') as $format)
                        <div class="item" data-value="{{ $format['type'] }}">
                            {{ $format['name'] }}
                        </div>
                    @endforeach
                </div>
            </div>

            {{ ErrorRenderer::inline('format') }}
        </div>

        <button class="ui button primary" type="submit" name="submit" value="">
            @lang('misc.export')
        </button>
        <a href="{{ route('community.economy.payment.index', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
