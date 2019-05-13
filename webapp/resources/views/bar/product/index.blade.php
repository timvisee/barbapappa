@extends('layouts.app')

{{-- TODO: translate --}}
@section('title', 'Alle producten')

@php
    use \App\Http\Controllers\BarController;
    use \App\Http\Controllers\BarMemberController;
@endphp

@section('content')
    <h2 class="ui header">
        @yield('title')

        <div class="sub header">
            in
            <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}">
                {{ $bar->name }}
            </a>
        </div>
    </h2>

    <div class="ui vertical menu fluid">
        {!! Form::open(['action' => ['BarController@show', $bar->human_id], 'method' => 'GET', 'class' => 'ui form']) !!}
            <div class="item">
                <div class="ui transparent icon input">
                    {{ Form::text('q', Request::input('q'), [
                        'placeholder' => __('pages.products.search') . '...',
                    ]) }}
                    {{-- TODO: remove icon class? --}}
                    <i class="icon glyphicons glyphicons-search link"></i>
                </div>
            </div>
        {!! Form::close() !!}

        @forelse($products as $product)
            <a href="{{ route('bar.product.show', [
                        'barId' => $bar->id,
                        'productId' => $product->id,
                    ]) }}"
                class="item">
                {{ $product->displayName() }}
                {!! $product->formatPrice($currencies, BALANCE_FORMAT_LABEL) !!}
            </a>
        @empty
            <i class="item">No products...</i>
        @endforelse
    </div>

    {{-- TODO: show buttons for managers to edit products --}}
@endsection
