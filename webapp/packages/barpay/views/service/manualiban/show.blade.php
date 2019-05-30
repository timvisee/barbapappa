<table class="ui compact celled definition table">
    <tbody>
        <tr>
            <td>@lang('barpay::misc.accountHolder')</td>
            <td>{{ $serviceable->account_holder }}</td>
        </tr>
        <tr>
            <td>@lang('barpay::misc.iban')</td>
            <td>{{ $serviceable->iban }}</td>
        </tr>
        <tr>
            <td>@lang('barpay::misc.bic')</td>
            <td>
                @if(!empty($serviceable->bic))
                    {{ $serviceable->bic }}
                @else
                    <i>@lang('misc.unspecified')</i>
                @endif
            </td>
        </tr>
    </tbody>
</table>
