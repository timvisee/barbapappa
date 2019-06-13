@php
    $bunqAccount = $serviceable->bunqAccount;
@endphp
<div class="field disabled {{ ErrorRenderer::hasError('bunq_account') ? 'error' : '' }}">
    {{ Form::label('bunq_account', __('pages.bunqAccounts.bunqAccount') . ':') }}

    <div class="ui fluid selection dropdown">
        {{ Form::hidden('bunq_account', $bunqAccount->id) }}
        <i class="dropdown icon"></i>

        <div class="default text">@lang('misc.pleaseSpecify')</div>
        <div class="menu">
            <div class="item" data-value="{{ $bunqAccount->id }}">
                {{ $bunqAccount->name }}
            </div>
        </div>
    </div>

    {{ ErrorRenderer::inline('bunq_account') }}
</div>
