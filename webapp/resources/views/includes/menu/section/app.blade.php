@php
    use \App\Http\Controllers\AppController;

    if(!isset($r))
        $r = Route::currentRouteName() ?? 'error';
@endphp

<a href="{{ route('explore.community') }}" class="item {{ str_starts_with($r, 'explore.') ? ' active' : '' }}">
    <i class="glyphicons glyphicons-search"></i>
    @lang('pages.explore.title')
</a>
<a href="{{ route('account') }}" class="item {{ str_starts_with($r, 'account') || str_starts_with($r, 'profile') ? ' active' : '' }}">
    <i class="glyphicons glyphicons-user"></i>
    @lang('pages.account')
</a>
<a href="{{ route('payment.index') }}" class="item {{ str_starts_with($r, 'payment.') ? ' active' : '' }}">
    <i class="glyphicons glyphicons-small-payments"></i>
    @lang('pages.payments.title')
</a>
@if(perms(AppController::permsAdminister()))
    <a href="{{ route('app.manage') }}"
            class="item">
        <i class="glyphicons glyphicons-new-window"></i>
        @lang('misc.manage')
    </a>
@endif
