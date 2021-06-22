@extends('layouts.app')

@section('title', $member->name)
@php
    $menusection = 'bar_manage';

    use App\Perms\BarRoles;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open(['action' => [
        'BarMemberController@doEdit',
        $bar->human_id,
        $member->id,
    ], 'method' => 'PUT', 'class' => 'ui form']) !!}
        <div class="field {{ ErrorRenderer::hasError('role') ? 'error' : '' }}">
            {{ Form::label('role', __('misc.role')) }}

            <div class="ui fluid selection dropdown">
                {{ Form::hidden('role', $member->role) }}
                <i class="dropdown icon"></i>

                <div class="default text">@lang('misc.pleaseSpecify')</div>
                <div class="menu">
                    @foreach(BarRoles::roles() as $id => $name)
                        <div class="item" data-value="{{ $id }}">{{ $name }}</div>
                    @endforeach
                </div>
            </div>

            {{ ErrorRenderer::inline('role') }}
        </div>

        <div class="ui top attached warning message visible">
            <span class="halflings halflings-warning-sign"></span>
            @lang('pages.barMembers.incorrectMemberRoleWarning')
        </div>

        {{-- Show warning for modifying own role --}}
        @if($member->user_id == barauth()->getSessionUser()->id)
            <div class="ui attached warning message visible">
                <span class="halflings halflings-warning-sign"></span>
                @lang('pages.barMembers.ownRoleDowngradeWarning')
            </div>
        @endif

        {{-- Role change confirmation checkbox --}}
        <div class="ui bottom attached segment">
            <div class="required field {{ ErrorRenderer::hasError('confirm_role_change') ? 'error' : '' }}">
                <div class="ui checkbox">
                    {{ Form::checkbox('confirm_role_change', true, false, ['tabindex' => 0, 'class' => 'hidden']) }}
                    {{ Form::label('confirm_role_change', __('pages.barMembers.confirmRoleChange')) }}
                </div>
                <br />
                {{ ErrorRenderer::inline('confirm_role_change') }}
            </div>
        </div>

        <button class="ui button primary" type="submit">@lang('misc.saveChanges')</button>
        <a href="{{ route('bar.member.show', [
            'barId' => $bar->human_id,
            'memberId' => $member->id,
        ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>
    {!! Form::close() !!}
@endsection
