<div class="receipt-box">
    <table class="receipt">
        <tr>
            <td colspan="3" align="center">
                <strong>@lang('mail.receipts.receipt')</strong>
                @if(isset($receipt['from']))
                    <br>
                    <small>
                        <em>
                            {{ strtolower(__('misc.last')) }}
                            {{ $receipt['from']->longAbsoluteDiffForHumans($receipt['to']) }}
                        </em>
                    </small>
                @endif
            </td>
        </tr>
        <tr class="divider">
            <td colspan="3"><hr /></td>
        </tr>
        @if($receipt['products']->isNotEmpty())
            <tr>
                <td></td>
                <td><em>@lang('pages.bar.purchases'):</em></td>
                <td></td>
            </tr>
            @foreach($receipt['products'] as $product)
                <tr>
                    <td align="right">{{ $product['quantity'] ?? 1 }}×</td>
                    <td>{{ $product['name'] ?? __('pages.products.unknownProduct') }}</td>
                    <td align="right">{!! $product['cost']->formatAmount() !!}</td>
                </tr>
            @endforeach
            <tr class="divider">
                <td colspan="3"><hr /></td>
            </tr>
        @endif
        @if($receipt['others']->isNotEmpty())
            @foreach($receipt['others'] as $other)
                <tr>
                    <td align="right">
                        @if($other['quantity'] ?? 1 != 1)
                            {{ $other['quantity'] }}×
                        @endif
                    </td>
                    <td>{{ $other['name'] }}</td>
                    <td align="right">{!! $other['cost']->formatAmount() !!}</td>
                </tr>
            @endforeach
            <tr class="divider">
                <td colspan="3"><hr /></td>
            </tr>
            <tr>
                <td></td>
                <td>@lang('mail.receipts.subTotal')</td>
                <td align="right">{!! $receipt['subTotal']->formatAmount() !!}</td>
            </tr>
        @endif
        <tr>
            <td></td>
            <td><strong>@lang('mail.receipts.total')</strong></td>
            <td align="right"><strong>{!! $receipt['total']->formatAmount() !!}</strong></td>
        </tr>
    </table>
</div>
