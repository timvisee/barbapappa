@extends('layouts.app')

@section('title', __('pages.paymentService.service'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.paymentservice.show', $service);
@endphp

@php
    use \App\Http\Controllers\PaymentServiceController;

    // Define menulinks
    $menulinks[] = [
        'name' => __('general.goBack'),
        'link' => route('community.economy.payservice.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.type')</td>
                <td>{{ $service->displayName(true) }}</td>
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
            @if(!$service->trashed())
                <tr>
                    <td>@lang('misc.enabled')</td>
                    <td>{{ yesno($service->enabled) }}</td>
                </tr>
                <tr>
                    <td>@lang('pages.paymentService.supportDeposit')</td>
                    <td>{{ yesno($service->deposit) }}</td>
                </tr>
                <tr>
                    <td>@lang('pages.paymentService.supportWithdraw')</td>
                    <td>{{ yesno($service->withdraw) }}</td>
                </tr>
            @else
                <tr>
                    <td>@lang('misc.trashed')</td>
                    <td>
                        <span class="ui text red">
                            @include('includes.humanTimeDiff', ['time' => $service->deleted_at])
                        </span>
                    </td>
                </tr>
            @endif
            <tr>
                <td>@lang('misc.createdAt')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $service->created_at])</td>
            </tr>
            @if($service->created_at != $service->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $service->updated_at])</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="ui divider"></div>

    {{-- Embed servicable specific view --}}
    @include($serviceable::view('show'))

    <div class="ui divider"></div>

    @if(perms(PaymentServiceController::permsManage()))
        <p>
            <div class="ui buttons">
                @if(!$service->trashed())
                    <a href="{{ route('community.economy.payservice.edit', [
                                'communityId' => $community->human_id,
                                'economyId' => $economy->id,
                                'serviceId' => $service->id,
                            ]) }}"
                            class="ui button secondary">
                        @lang('misc.edit')
                    </a>
                @else
                    {{-- <a href="{{ route('community.economy.payservice.restore', [ --}}
                    {{--             'communityId' => $community->human_id, --}}
                    {{--             'economyId' => $economy->id, --}}
                    {{--             'serviceId' => $service->id, --}}
                    {{--         ]) }}" --}}
                    {{--         class="ui button primary"> --}}
                    {{--     @lang('misc.restore') --}}
                    {{-- </a> --}}
                @endif
                <a href="{{ route('community.economy.payservice.delete', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'serviceId' => $service->id,
                        ]) }}"
                        class="ui button negative">
                    @lang('misc.delete')
                </a>
            </div>
        </p>
    @endif

    <p>
        <a href="{{ route('community.economy.payservice.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
