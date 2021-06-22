@php
    use App\Http\Controllers\AppBunqAccountController;

    if(!isset($r))
        $r = Route::currentRouteName();
@endphp

<div class="item header spaced">@lang('pages.app.manageApp'):</div>

<a href="{{ route('app.manage') }}"
        class="item {{ $r == 'app.manage' ? ' active' : '' }}">
    <i class="glyphicons glyphicons-edit"></i>
    @lang('misc.manage')
</a>
@if(perms(AppBunqAccountController::permsView()))
    <a href="{{ route('app.bunqAccount.index') }}"
            class="item {{ str_starts_with($r, 'app.bunqAccount.') ? ' active' : '' }}">
        <i class="glyphicons glyphicons-credit-card"></i>
        @lang('pages.bunqAccounts.title')
    </a>
@endif
