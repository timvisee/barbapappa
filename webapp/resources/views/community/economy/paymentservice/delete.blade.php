@extends('layouts.app')

@section('title', __('pages.paymentService.deleteService'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.paymentService.deleteQuestion')</p>

    {!! Form::open([
        'action' => [
            'PaymentServiceController@doDelete',
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'serviceId' => $service->id,
        ],
        'method' => 'DELETE',
        'class' => 'ui form'
    ]) !!}
        <div class="ui info top attached message visible">
            <span class="halflings halflings-info-sign"></span>
            @lang('pages.paymentService.startedWillComplete')
        </div>

        <div class="ui info bottom attached message visible">
            <span class="halflings halflings-info-sign"></span>
            @lang('misc.trashingCanBeUndone')
        </div>

        <div class="ui divider hidden"></div>

        <div class="ui buttons">
            <a href="{{ route('community.economy.payservice.show', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                        'serviceId' => $service->id,
                    ]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesRemove')</button>
        </div>
    {!! Form::close() !!}
@endsection
