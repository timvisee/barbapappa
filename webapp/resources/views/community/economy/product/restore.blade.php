@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.products.restoreQuestion')</p>

    <br />

    {!! Form::open([
        'action' => [
            'ProductController@doRestore',
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'productId' => $product->id,
        ],
        'method' => 'PUT',
        'class' => 'ui form'
    ]) !!}
        <div class="ui buttons">
            <a href="{{ route('community.economy.product.show', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                        'productId' => $product->id,
                    ]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesRestore')</button>
        </div>
    {!! Form::close() !!}
@endsection
