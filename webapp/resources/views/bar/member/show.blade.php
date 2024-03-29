@extends('layouts.app')

@section('title', $member->name)
@php
    $breadcrumbs = Breadcrumbs::generate('bar.member.show', $member);
    $menusection = 'bar_manage';

    use App\Http\Controllers\BarMemberController;
    use App\Http\Controllers\CommunityMemberController;
    use App\Perms\BarRoles;
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
                <td>{{ BarRoles::roleName($member->role) }}</td>
            </tr>
            @if($economy_member)
                <tr>
                    <td>@lang('pages.barMembers.nickname')</td>
                    @if(!empty($economy_member->nickname))
                        <td>{{ $economy_member->nickname }}</td>
                    @else
                        <td><i>@lang('misc.none')</i></td>
                    @endif
                </tr>
                <tr>
                    <td>@lang('misc.tags')</td>
                    @if(!empty($economy_member->tags))
                        <td>{{ $economy_member->tags }}</td>
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
            @endif
            @if(perms(BarMemberController::permsManage()))
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
                    <td>@lang('pages.barMembers.lastVisit')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => new Carbon($member->visited_at)])</td>
                </tr>
            @endif
            <tr>
                <td>@lang('pages.barMembers.memberSince')</td>
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

    @if(perms(BarMemberController::permsManage()))
        <p>
            <div class="ui buttons">
                <a href="{{ route('bar.member.edit', [
                    'barId' => $bar->human_id,
                    'memberId' => $member->id
                ]) }}"
                        class="ui button secondary">
                    @lang('misc.edit')
                </a>
                <a href="{{ route('bar.member.delete', [
                    'barId' => $bar->human_id,
                    'memberId' => $member->id,
                ]) }}"
                        class="ui button negative">
                    @lang('misc.delete')
                </a>
            </div>
        </p>
    @endif

    @if(perms(BarMemberController::permsView()))
        @php
            $econ_member = $member->fetchEconomyMember();
        @endphp
        <div class="ui vertical menu fluid">
            <h5 class="ui item header">@lang('pages.wallets.title')</h5>
            @forelse($econ_member->wallets as $wallet)
                <a href="{{ route('community.wallet.show', [
                    'communityId' => $community->human_id,
                    'economyId' => $econ_member->economy_id,
                    'walletId' => $wallet->id,
                ]) }}" class="item">
                    {{ $wallet->name }}
                    {!! $wallet->formatBalance(BALANCE_FORMAT_LABEL) !!}
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
            $community_member = $member->fetchCommunityMember();
        @endphp
        <div class="ui vertical menu fluid">
            <h5 class="ui item header">@lang('misc.community')</h5>
            @if($community_member)
                <a href="{{ route('community.member.show', [
                    'communityId' => $community->human_id,
                    'memberId' => $community_member->id,
                ]) }}" class="item">
                    {{ $community_member->name }}
                    <span class="subtle">@lang('misc.in') {{ $member->bar->community->name }}</span>
                </a>
            @else
                <div class="item">
                    <i>@lang('misc.none')...</i>
                </div>
            @endif
        </div>
    @endif

    <p>
        <a href="{{ route('bar.member.index', ['barId' => $bar->human_id]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
