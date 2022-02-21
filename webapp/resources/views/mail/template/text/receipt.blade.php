================
@lang('mail.receipts.receipt') @if(isset($receipt['from']))
({{ strtolower(__('misc.last')) }} {{ $receipt['from']->longAbsoluteDiffForHumans($receipt['to']) }})
@endif
----------------
@if($receipt['products']->isNotEmpty())
@lang('pages.bar.purchases'):
@foreach($receipt['products'] as $product)
{{ $product['quantity'] ?? 1 }}× {{ $product['name'] ?? __('pages.products.unknownProduct') }} {!! $product['cost']->formatAmount() !!}
@endforeach
----------------
@endif
@if($receipt['others']->isNotEmpty())
@foreach($receipt['others'] as $other)
@if($other['quantity'] ?? 1 != 1){{ $other['quantity'] }}× @endif{{ $other['name'] }} {!! $other['cost']->formatAmount() !!}
@endforeach
----------------
@lang('mail.receipts.subTotal') {!! $receipt['subTotal']->formatAmount() !!}
@endif
@lang('mail.receipts.total') {!! $receipt['total']->formatAmount() !!}
================
