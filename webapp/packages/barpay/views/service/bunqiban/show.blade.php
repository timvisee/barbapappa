{{-- TODO: hyperlink bunq account --}}
<table class="ui compact celled definition table">
    <tbody>
        <tr>
            <td>@lang('pages.bunqAccounts.bunqAccount')</td>
            <td>
                @php
                    $bunqAccount = $serviceable->bunqAccount()->withoutGlobalScopes()->first();
                @endphp
                @if($bunqAccount != null)
                    {{ $bunqAccount->name }}
                @else
                    <i>@lang('misc.unknown')</i>
                @endif
            </td>
        </tr>
        <tr>
            <td>@lang('barpay::misc.accountHolder')</td>
            <td>{{ $serviceable->account_holder }}</td>
        </tr>
        <tr>
            <td>@lang('barpay::misc.iban')</td>
            <td>{{ format_iban($serviceable->iban) }}</td>
        </tr>
        <tr>
            <td>@lang('barpay::misc.bic')</td>
            <td>
                @if(!empty($serviceable->bic))
                    {{ format_bic($serviceable->bic) }}
                @else
                    <i>@lang('misc.unspecified')</i>
                @endif
            </td>
        </tr>
    </tbody>
</table>
