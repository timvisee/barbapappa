@extends('layouts.app')

@section('title', __('pages.paymentService.newService'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open([
        'action' => [
            'PaymentServiceController@doCreate',
            $community->human_id,
            $economy->id,
        ],
        'method' => 'POST',
        'class' => 'ui form'
    ]) !!}
        {{ Form::hidden('serviceable', $serviceable) }}

        <div class="field disabled">
            {{ Form::label('type', __('pages.paymentService.serviceType') . ':') }}
            {{ Form::text('type', $serviceable::name()) }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('enabled') ? 'error' : '' }}">
            <div class="ui checkbox">
                <input type="checkbox"
                        name="enabled"
                        tabindex="0"
                        class="hidden"
                        checked="checked">
                {{ Form::label('enabled', __('pages.paymentService.enabledDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('enabled') }}
        </div>

        <div class="ui divider"></div>

        <div class="field {{ ErrorRenderer::hasError('account_holder') ? 'error' : '' }}">
            {{ Form::label('account_holder', __('barpay::misc.accountHolder') . ':') }}
            {{ Form::text('account_holder', '', [
                'placeholder' => __('account.firstNamePlaceholder') . ' ' .  __('account.lastNamePlaceholder'),
            ]) }}
            {{ ErrorRenderer::inline('account_holder') }}
        </div>

        <div class="two fields">
            <div class="field {{ ErrorRenderer::hasError('iban') ? 'error' : '' }}">
                {{ Form::label('iban', __('barpay::misc.iban') . ':') }}
                {{ Form::text('iban', '', [
                    'placeholder' => __('barpay::misc.ibanPlaceholder'),
                ]) }}
                {{ ErrorRenderer::inline('iban') }}
            </div>

            <div class="field {{ ErrorRenderer::hasError('bic') ? 'error' : '' }}">
                {{ Form::label('bic', __('barpay::misc.bic') .  ' (' .  __('general.optional') . '):') }}
                {{ Form::text('bic', '', [
                    'placeholder' => __('barpay::misc.bicPlaceholder'),
                ]) }}
                {{ ErrorRenderer::inline('bic') }}
            </div>
        </div>

        {{-- <div class="two fields"> --}}
        {{--     <div class="inline field {{ ErrorRenderer::hasError('allow_deposit') ? 'error' : '' }}"> --}}
        {{--         <div class="ui toggle checkbox"> --}}
        {{--             <input type="checkbox" --}}
        {{--                     name="allow_depost" --}}
        {{--                     tabindex="0" --}}
        {{--                     class="hidden" --}}
        {{--                     checked="checked"> --}}
        {{--             {1{-- TODO: translate --}1} --}}
        {{--             {{ Form::label('allow_depost', __('Allow deposits')) }} --}}
        {{--         </div> --}}
        {{--         <br /> --}}
        {{--         {{ ErrorRenderer::inline('allow_depost') }} --}}
        {{--     </div> --}}
        {{--     <div class="inline field {{ ErrorRenderer::hasError('allow_withdraw') ? 'error' : '' }}"> --}}
        {{--         <div class="ui toggle checkbox"> --}}
        {{--             <input type="checkbox" --}}
        {{--                     name="allow_withdraw" --}}
        {{--                     tabindex="0" --}}
        {{--                     class="hidden" --}}
        {{--                     checked="checked"> --}}
        {{--             {1{-- TODO: translate --}1} --}}
        {{--             {{ Form::label('allow_withdraw', __('Allow withdrawals')) }} --}}
        {{--         </div> --}}
        {{--         <br /> --}}
        {{--         {{ ErrorRenderer::inline('allow_withdraw') }} --}}
        {{--     </div> --}}
        {{-- </div> --}}

        {{-- <div class="four fields"> --}}
        {{--     @php --}}
        {{--         $field = 'tmp'; --}}
        {{--     @endphp --}}
        {{--     <div class="field {{ ErrorRenderer::hasError($field) ? 'error' : '' }}"> --}}
        {{--         <label>Deposit minimum:</label> --}}
        {{--         <div class="ui labeled input"> --}}
        {{--             <label for="{{ $field }}" class="ui label">€</label> --}}
        {{--             <input type="text" --}}
        {{--                 placeholder="0.01" --}}
        {{--                 id="{{ $field }}" --}}
        {{--                 name="{{ $field }}" --}}
        {{--                 value="0.01" /> --}}
        {{--         </div> --}}
        {{--         {{ ErrorRenderer::inline($field) }} --}}
        {{--     </div> --}}
        {{--     <div class="field {{ ErrorRenderer::hasError($field) ? 'error' : '' }}"> --}}
        {{--         <label>Deposit maximum:</label> --}}
        {{--         <div class="ui labeled input"> --}}
        {{--             <label for="{{ $field }}" class="ui label">€</label> --}}
        {{--             <input type="text" --}}
        {{--                 placeholder="1000" --}}
        {{--                 id="{{ $field }}" --}}
        {{--                 name="{{ $field }}" --}}
        {{--                 value="1000" /> --}}
        {{--         </div> --}}
        {{--         {{ ErrorRenderer::inline($field) }} --}}
        {{--     </div> --}}
        {{--     <div class="field {{ ErrorRenderer::hasError($field) ? 'error' : '' }}"> --}}
        {{--         <label>Withdraw minimum:</label> --}}
        {{--         <div class="ui labeled input"> --}}
        {{--             <label for="{{ $field }}" class="ui label">€</label> --}}
        {{--             <input type="text" --}}
        {{--                 placeholder="0.01" --}}
        {{--                 id="{{ $field }}" --}}
        {{--                 name="{{ $field }}" --}}
        {{--                 value="0.01" /> --}}
        {{--         </div> --}}
        {{--         {{ ErrorRenderer::inline($field) }} --}}
        {{--     </div> --}}
        {{--     <div class="field {{ ErrorRenderer::hasError($field) ? 'error' : '' }}"> --}}
        {{--         <label>Withdraw maximum:</label> --}}
        {{--         <div class="ui labeled input"> --}}
        {{--             <label for="{{ $field }}" class="ui label">€</label> --}}
        {{--             <input type="text" --}}
        {{--                 placeholder="1000" --}}
        {{--                 id="{{ $field }}" --}}
        {{--                 name="{{ $field }}" --}}
        {{--                 value="1000" /> --}}
        {{--         </div> --}}
        {{--         {{ ErrorRenderer::inline($field) }} --}}
        {{--     </div> --}}
        {{-- </div> --}}

        <div class="ui divider"></div>

        <br />

        <button class="ui button primary" type="submit" name="submit" value="">
            @lang('misc.add')
        </button>
        <a href="{{ route('community.economy.payservice.index', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
        ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
