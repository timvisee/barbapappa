@extends('layouts.app')

@section('title', $member->name)
@php
    $breadcrumbs = Breadcrumbs::generate('community.member.show', $member);
    $menusection = 'community_manage';

    use App\Http\Controllers\CommunityMemberController;
    use App\Perms\CommunityRoles;
    use \Carbon\Carbon;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.user')</td>
                <td>{{ $member->name }}</td>
            </tr>
            <tr>
                <td>@lang('misc.role')</td>
                <td>{{ CommunityRoles::roleName($member->role) }}</td>
            </tr>
            @if(perms(CommunityMemberController::permsManage()))
                <tr>
                    <td>@lang('account.emails')</td>
                    <td>
                        <div class="ui bulleted list">
                            @if($member->user?->emails != null)
                                @forelse($member->user->emails as $email)
                                    <div class="item">
                                        <a href="mailto:{{ $email->email }}">{{ $email->email }}</a>
                                        @if(!$email->isVerified())
                                            <span class="subtle">(@lang('misc.unverified'))</span>
                                        @endif
                                    </div>
                                @empty
                                    <i class="item">@lang("account.noEmails")</i>
                                @endforelse
                            @endif
                        </div>
                    </td>
                </tr>
            @endif
            @if($member->visited_at)
                <tr>
                    <td>@lang('pages.communityMembers.lastVisit')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => new Carbon($member->visited_at)])</td>
                </tr>
            @endif
            <tr>
                <td>@lang('pages.communityMembers.memberSince')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $member->created_at])</td>
            </tr>
            @if($member->created_at != $member->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $member->updated_at])</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if(perms(CommunityMemberController::permsManage()))
        <p>
            <div class="ui buttons">
                <a href="{{ route('community.member.edit', [
                    'communityId' => $community->human_id,
                    'memberId' => $member->id,
                ]) }}"
                        class="ui button secondary">
                    @lang('misc.edit')
                </a>
                <a href="{{ route('community.member.delete', [
                    'communityId' => $community->human_id,
                    'memberId' => $member->id,
                ]) }}"
                        class="ui button negative">
                    @lang('misc.delete')
                </a>
            </div>
        </p>
    @endif

    @if(perms(CommunityMemberController::permsView()))
        @php
            $econ_members = $member->fetchEconomyMembers();
            $wallets = $econ_members
                ->flatMap(function($member) {
                    return $member->wallets;
                })
                ->filter(function($wallet) {
                    return $wallet->economyMember?->economy != null;
                });
        @endphp
        <div class="ui vertical menu fluid">
            <h5 class="ui item header">@lang('pages.wallets.title')</h5>
            @forelse($wallets as $wallet)
                <a href="{{ route('community.wallet.show', [
                    'communityId' => $community->human_id,
                    'economyId' => $wallet->economyMember->economy_id,
                    'walletId' => $wallet->id,
                ]) }}" class="item">
                    {{ $wallet->name }}
                    {!! $wallet->formatBalance(BALANCE_FORMAT_LABEL) !!}
                    <span class="subtle">@lang('misc.in') {{ $wallet->economyMember->economy->name }}</span>
                </a>
            @empty
                <div class="item">
                    <i>@lang('pages.wallets.noWallets')</i>
                </div>
            @endforelse
        </div>
    @endif

    @if(perms(CommunityMemberController::permsView()))
        @php
            $bar_members = $member->fetchBarMembers();
        @endphp
        <div class="ui vertical menu fluid">
            <h5 class="ui item header">@lang('pages.bars')</h5>
            @forelse($bar_members as $bar_member)
                <a href="{{ route('bar.member.show', [
                    'communityId' => $bar_member->bar->community->human_id,
                    'barId' => $bar_member->bar->human_id,
                    'memberId' => $bar_member->id,
                ]) }}" class="item">
                    {{ $bar_member->name }}
                    <span class="subtle">@lang('misc.in') {{ $bar_member->bar->name }}</span>
                </a>
            @empty
                <div class="item">
                    <i>@lang('misc.none')...</i>
                </div>
            @endforelse
        </div>
    @endif

    <a href="{{ route('community.member.index', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('general.goBack')
    </a>
@endsection
