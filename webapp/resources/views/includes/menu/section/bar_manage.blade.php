@php
    use \App\Http\Controllers\BarController;
    use \App\Http\Controllers\BarMemberController;
    use \App\Http\Controllers\CommunityController;
    use \App\Http\Controllers\EconomyController;
    use \App\Http\Controllers\ProductController;

    if(!isset($r))
        $r = Route::currentRouteName() ?? 'error';
@endphp

<div class="item header spaced">@lang('misc.manage') {{ $bar->name }}:</div>

<a href="{{ route('bar.manage', ['barId' => $bar->human_id]) }}"
        class="item {{ $r == 'bar.manage' ? ' active' : '' }}">
    <i class="glyphicons glyphicons-edit"></i>
    @lang('misc.manage')
</a>
@if(perms(BarMemberController::permsView()))
    <a href="{{ route('bar.member.index', ['barId' => $bar->human_id]) }}"
            class="item {{ str_starts_with($r, 'bar.member.') ? ' active' : '' }}">
        <i class="glyphicons glyphicons-user-structure"></i>
        @lang('misc.members')
    </a>
@endif
@if(perms(EconomyController::permsView()))
    <a href="{{ route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $bar->economy_id]) }}"
            class="item">
        <i class="glyphicons glyphicons-new-window"></i>
        @lang('pages.economies.title')
    </a>
@endif
@if(perms(ProductController::permsView()))
    <a href="{{ route('community.economy.product.index', ['communityId' => $community->human_id, 'economyId' => $bar->economy_id]) }}"
            class="item">
        <i class="glyphicons glyphicons-new-window"></i>
        @lang('pages.products.title')
    </a>
@endif
@if(perms(BarController::permsManage()))
    <a href="{{ route('bar.history', ['barId' => $bar->human_id]) }}"
            class="item {{ $r == 'bar.history' ? ' active' : '' }}">
        <i class="glyphicons glyphicons-history"></i>
        @lang('pages.bar.purchases')
    </a>
    <a href="{{ route('bar.links', ['barId' => $bar->human_id]) }}"
            class="item {{ $r == 'bar.links' ? ' active' : '' }}">
        <i class="glyphicons glyphicons-link"></i>
        @lang('misc.links')
    </a>
    <a href="{{ route('bar.kiosk.start', ['barId' => $bar->human_id]) }}"
            class="item {{ $r == 'bar.kiosk.start' ? ' active' : '' }}">
        <i class="glyphicons glyphicons-shop"></i>
        @lang('pages.bar.startKiosk')
    </a>
    <a href="{{ route('bar.kiosk.sessions.index', ['barId' => $bar->human_id]) }}"
            class="item {{ str_starts_with($r, 'bar.kiosk.sessions.') ? ' active' : '' }}">
        <i class="glyphicons glyphicons-shop"></i>
        @lang('pages.bar.kioskSessions')
    </a>
    <a href="{{ route('bar.poster.generate', ['barId' => $bar->human_id]) }}"
            class="item {{ $r == 'bar.poster.generate' ? ' active' : '' }}">
        <i class="glyphicons glyphicons-qrcode"></i>
        @lang('misc.poster')
    </a>
@endif

{{-- Bar menu --}}
@include('includes.menu.section.bar')
