{{-- Authenticated header --}}
<div class="item header has-alt-button">
    {{ trans_random('general.hellos') }}
    {{ barauth()->getSessionUser()->name }}

    <a href="{{ route('logout') }}"
            class="alt-button logout"
            title="@lang('auth.logout')">
        <i class="glyphicons glyphicons-exit"></i>
    </a>
</div>

@if(isset($menusection))
    @include('includes.menu.section.' . $menusection)

    <div class="item header spaced">@lang('misc.app'):</div>
@endif

@include('includes.menu.section.app')
