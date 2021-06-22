@php
    use App\Http\Controllers\BunqAccountController;
    use App\Http\Controllers\CommunityController;
    use App\Http\Controllers\CommunityMemberController;
    use App\Http\Controllers\EconomyController;

    if(!isset($r))
        $r = Route::currentRouteName();
@endphp

<div class="item header spaced">@lang('misc.manage') {{ $community->name }}:</div>

<a href="{{ route('community.manage', ['communityId' => $community->human_id]) }}"
        class="item {{ $r == 'community.manage' ? ' active' : '' }}">
    <i class="glyphicons glyphicons-edit"></i>
    @lang('misc.manage')
</a>
@if(perms(EconomyController::permsView()))
    <a href="{{ route('community.economy.index', ['communityId' => $community->human_id]) }}"
            class="item {{ str_starts_with($r, 'community.economy.') ? ' active' : '' }}">
        <i class="glyphicons glyphicons-money"></i>
        @lang('pages.economies.title')
    </a>
@endif
@if(perms(CommunityMemberController::permsView()))
    <a href="{{ route('community.member.index', ['communityId' => $community->human_id]) }}"
            class="item {{ str_starts_with($r, 'community.member.') ? ' active' : '' }}">
        <i class="glyphicons glyphicons-user-structure"></i>
        @lang('misc.members')
    </a>
@endif
@if(perms(BunqAccountController::permsView()))
    <a href="{{ route('community.bunqAccount.index', ['communityId' => $community->human_id]) }}"
            class="item {{ str_starts_with($r, 'community.bunqAccount.') ? ' active' : '' }}">
        <i class="glyphicons glyphicons-credit-card"></i>
        @lang('pages.bunqAccounts.title')
    </a>
@endif
@if(perms(CommunityController::permsManage()))
    <a href="{{ route('community.links', ['communityId' => $community->human_id]) }}"
            class="item {{ $r == 'community.links' ? ' active' : '' }}">
        <i class="glyphicons glyphicons-link"></i>
        @lang('misc.links')
    </a>
    <a href="{{ route('community.poster.generate', ['communityId' => $community->human_id]) }}"
            class="item {{ $r == 'community.poster.generate' ? ' active' : '' }}">
        <i class="glyphicons glyphicons-qrcode"></i>
        @lang('misc.poster')
    </a>
@endif

{{-- Community menu --}}
@include('includes.menu.section.community')
