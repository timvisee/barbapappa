@extends('layouts.app')

@section('title', $member->name)

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.barMembers.deleteQuestion')</p>

    <div class="ui warning message visible">
        <span class="halflings halflings-warning-sign"></span>
        @lang('misc.cannotBeUndone')
    </div>

    {{-- TODO: toggle to also remove user from community --}}
    {{-- TODO: toggle to also remove user from other bars in community --}}

    {!! Form::open(['action' => [
        'BarMemberController@doDelete',
        'barId' => $bar->human_id,
        'memberId' => $member->id,
    ], 'method' => 'DELETE', 'class' => 'ui form']) !!}
        {{-- Self delete confirmation checkbox --}}
        @if($member->user_id == barauth()->getSessionUser()->id)
            <div class="field {{ ErrorRenderer::hasError('confirm_self_delete') ? 'error' : '' }}">
                <div class="ui checkbox">
                    {{ Form::checkbox('confirm_self_delete', true, false, ['tabindex' => 0, 'class' => 'hidden']) }}
                    {{ Form::label('confirm_self_delete', __('pages.barMembers.confirmSelfDelete')) }}
                </div>
                <br />
                {{ ErrorRenderer::inline('confirm_self_delete') }}
            </div>
        @endif

        <br />

        <div class="ui buttons">
            <a href="{{ route('bar.member.show', [
                'barId' => $bar->human_id,
                'memberId' => $member->id,
            ]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesRemove')</button>
        </div>
    {!! Form::close() !!}
@endsection
