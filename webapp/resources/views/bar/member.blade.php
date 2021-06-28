@extends('layouts.app')

@section('title', __('pages.barMember.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('bar.member', $bar);
    $menusection = 'bar';

    use \App\Http\Controllers\BarMemberController;
@endphp

@section('content')
    {{-- Joined, leave button --}}
    <div class="ui success message visible">
        <div class="header">@lang('pages.bar.joined')</div>
        <p>@lang('pages.bar.youAreJoined')</p>
        <a href="{{ route('bar.leave', ['barId' => $bar->human_id]) }}"
                class="ui button small basic">
            @lang('pages.bar.leave')
        </a>
    </div>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('pages.barMembers.nickname')</td>
                @if(!empty($economy_member->nickname))
                    <td>{{ $economy_member->nickname }}</td>
                @else
                    <td><i>@lang('misc.none')</i></td>
                @endif
            </tr>
            <tr>
                <td>@lang('pages.barMember.showInBuy')</td>
                <td>{{ yesno($economy_member->show_in_buy) }}</td>
            </tr>
            <tr>
                <td>@lang('pages.barMember.showInKiosk')</td>
                <td>{{ yesno($economy_member->show_in_kiosk) }}</td>
            </tr>
            <tr>
                <td>@lang('pages.barMembers.memberSince')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $bar_member->created_at])</td>
            </tr>
        </tbody>
    </table>

    <p>
        <a href="{{ route('bar.editMember', ['barId' => $bar->human_id]) }}"
                class="ui primary button basic">
            @lang('misc.change')
        </a>

        <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}"
                class="ui button basic">
            @lang('pages.bar.backToBar')
        </a>
    </p>
@endsection
