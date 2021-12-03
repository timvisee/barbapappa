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

        {{-- Visibility toggles --}}
        @if($economy_member)
            <div class="ui divider"></div>

            <div class="ui message">
                <div class="header">@lang('pages.barMembers.nickname')</div>
                <p>@lang('pages.barMembers.nicknameDescription')</p>
            </div>

            <div class="field {{ ErrorRenderer::hasError('nickname') ? 'error' : '' }}">
                {{ Form::label('nickname', __('pages.barMembers.nickname') . ':') }}
                {{ Form::text('nickname', $economy_member->nickname, ['placeholder' => '']) }}
                {{ ErrorRenderer::inline('nickname') }}
            </div>

            <div class="ui hidden divider"></div>

            <div class="ui message">
                <div class="header">@lang('misc.tags')</div>
                <p>@lang('pages.barMembers.tagsDescription')</p>
            </div>

            <div class="field {{ ErrorRenderer::hasError('tags') ? 'error' : '' }}">
                {{ Form::label('tags', __('misc.tags') . ':') }}
                {{ Form::text('tags', $economy_member->tags, ['placeholder' => '']) }}
                {{ ErrorRenderer::inline('tags') }}
            </div>

            <div class="ui hidden divider"></div>

            <div class="ui message">
                <div class="header">@lang('pages.barMember.visibility')</div>
                <p>@lang('pages.barMember.visibilityDescription')</p>
            </div>

            <div class="inline field">
                <div class="ui toggle checkbox">
                    {{ Form::checkbox('show_in_buy', true, $economy_member->show_in_buy, ['tabindex' => 0, 'class' => 'hidden']) }}
                    {{ Form::label('show_in_buy', __('pages.barMember.showInBuy') . ' (' . __('general.recommended') . ')') }}
                </div>
                {{ ErrorRenderer::inline('show_in_buy') }}
            </div>

            <div class="inline field">
                <div class="ui toggle checkbox">
                    {{ Form::checkbox('show_in_kiosk', true, $economy_member->show_in_kiosk, ['tabindex' => 0, 'class' => 'hidden']) }}
                    {{ Form::label('show_in_kiosk', __('pages.barMember.showInKiosk') . ' (' . __('general.recommended') . ')') }}
                </div>
                {{ ErrorRenderer::inline('show_in_kiosk') }}
            </div>
        @endif

        <div class="ui hidden divider"></div>

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
