@extends('layouts.app')

@section('title', $product->name)

@php
    use \App\Http\Controllers\ProductController;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.name')</td>
                <td>
                    <div class="ui list">
                        <div class="item">{{ $product->name }}</div>
                        @foreach($product->names as $name)
                            <div class="item">
                                <i>{{ $name->languageName() }}:</i>
                                {{ $name->name }}
                            </div>
                        @endforeach
                    </div>
                </td>
            </tr>
            <tr>
                <td>@lang('pages.products.prices')</td>
                <td>
                    @if($product->prices->isNotEmpty())
                        <div class="ui list">
                            @foreach($product->prices as $price)
                                <div class="item">{{ $price->formatPrice() }}</div>
                            @endforeach
                        </div>
                    @else
                        {{-- TODO: use style for this --}}
                        <i style="color: red;">@lang('misc.none')</i>
                    @endif
                </td>
            </tr>
            <tr>
                <td>@lang('misc.type')</td>
                <td>{{ $product->typeName() }}</td>
            </tr>
            <tr>
                <td>@lang('pages.community.economy')</td>
                <td>
                    <a href="{{ route('community.economy.show', [
                                'communityId'=> $community->human_id,
                                'economyId' => $economy->id
                            ]) }}">
                        {{ $economy->name }}
                    </a>
                </td>
            </tr>
            @if($product->user_id != null)
                <tr>
                    <td>@lang('misc.createdBy')</td>
                    <td>{{ $product->user->name }}</td>
                </tr>
            @endif
            <tr>
                <td>@lang('misc.enabled')</td>
                <td>{{ yesno($product->enabled) }}</td>
            </tr>
            <tr>
                <td>@lang('misc.archived')</td>
                <td>{{ yesno($product->archived) }}</td>
            </tr>
            <tr>
                <td>@lang('misc.createdAt')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $product->created_at])</td>
            </tr>
            @if($product->created_at != $product->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $product->updated_at])</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if(perms(ProductController::permsManage()))
        <p>
            <div class="ui buttons">
                <a href="{{ route('community.economy.product.edit', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'productId' => $product->id
                ]) }}"
                        class="ui button secondary">
                    @lang('misc.edit')
                </a>
                <a href="{{ route('community.economy.product.delete', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'productId' => $product->id
                ]) }}"
                        class="ui button negative">
                    @lang('misc.delete')
                </a>
            </div>
        </p>
    @endif

    <p>
        <a href="{{ route('community.economy.product.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection