<div class="field {{ ErrorRenderer::hasError('account_holder') ? 'error' : '' }}">
    {{ Form::label('account_holder', __('barpay::misc.accountHolder') . ':') }}
    {{ Form::text('account_holder', $serviceable->account_holder, [
        'placeholder' => __('account.firstNamePlaceholder') . ' ' .  __('account.lastNamePlaceholder'),
    ]) }}
    {{ ErrorRenderer::inline('account_holder') }}
</div>

<div class="two fields">
    <div class="field {{ ErrorRenderer::hasError('iban') ? 'error' : '' }}">
        {{ Form::label('iban', __('barpay::misc.iban') . ':') }}
        {{ Form::text('iban', $serviceable->iban, [
            'placeholder' => __('barpay::misc.ibanPlaceholder'),
        ]) }}
        {{ ErrorRenderer::inline('iban') }}
    </div>

    <div class="field {{ ErrorRenderer::hasError('bic') ? 'error' : '' }}">
        {{ Form::label('bic', __('barpay::misc.bic') .  ' (' .  __('general.optional') . '):') }}
        {{ Form::text('bic', $serviceable->bic, [
            'placeholder' => __('barpay::misc.bicPlaceholder'),
        ]) }}
        {{ ErrorRenderer::inline('bic') }}
    </div>
</div>
