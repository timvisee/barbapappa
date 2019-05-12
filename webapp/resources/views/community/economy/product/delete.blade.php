@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.products.deleteQuestion')</p>

    <div class="ui warning message visible">
        <span class="halflings halflings-warning-sign"></span>
        @lang('misc.cannotBeUndone')
    </div>

    <br />

    {!! Form::open([
        'action' => [
            'ProductController@doDelete',
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'productId' => $product->id,
        ],
        'method' => 'DELETE',
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
            <button class="ui button positive basic" type="submit">@lang('general.yesRemove')</button>
        </div>
    {!! Form::close() !!}
@endsection
