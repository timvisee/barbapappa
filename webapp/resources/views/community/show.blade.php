@extends('layouts.app')

@section('title', $community->name)
@php
    $breadcrumbs = Breadcrumbs::generate('community.show', $community);
    $menusection = 'community';
@endphp

@section('content')
    @include('community.include.communityHeader')
    @include('community.include.joinBanner')

    @include('bar.include.list', [
        'header' => __('pages.bars') . ' (' . count($bars) . ')',
    ])
@endsection
