@php
    use \App\Http\Controllers\CommunityController;
    $r = Route::currentRouteName();
@endphp

<div class="item header spaced">{{ $community->name }}:</div>

<a href="{{ route('community.show', ['communityId' => $community->human_id]) }}"
        class="item {{ $r == 'community.show' ? ' active' : '' }}">
    <i class="glyphicons glyphicons-group"></i>
    @lang('misc.community')
</a>
<a href="{{ route('community.info', ['communityId' => $community->human_id]) }}"
        class="item {{ $r == 'community.info' ? ' active' : '' }}">
    <i class="glyphicons glyphicons-info-sign"></i>
    @lang('misc.information')
</a>
@if(perms(CommunityController::permsUser()))
    <a href="{{ route('community.stats', ['communityId' => $community->human_id]) }}"
            class="item {{ $r == 'community.stats' ? ' active' : '' }}">
        <i class="glyphicons glyphicons-stats"></i>
        @lang('pages.stats.title')
    </a>
@endif
@if($community->isJoined(barauth()->getSessionUser()))
    <a href="{{ route('community.wallet.index', ['communityId' => $community->human_id]) }}"
            class="item {{ str_starts_with($r, 'community.wallet.') ? ' active' : '' }}">
        <i class="glyphicons glyphicons-wallet"></i>
        @lang('pages.wallets.myWallets')
    </a>
@endif
@if(perms(CommunityController::permsManage()))
    <a href="{{ route('community.manage', ['communityId' => $community->human_id]) }}"
            class="item">
        <i class="glyphicons glyphicons-new-window"></i>
        @lang('misc.manage')
    </a>
@endif
