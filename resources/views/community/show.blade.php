@extends('layouts.app')

@section('content')
    <h2 class="ui header">{{ $community->name }}</h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>ID</td>
                <td><a href="{{ route('community.show', ['communityId' => $community->id]) }}">{{ $community->id }}</a></td>
            </tr>
            <tr>
                <td>Name</td>
                <td>{{ $community->name }}</td>
            </tr>
            <tr>
                <td>Slug</td>
                @if($community->hasSlug())
                    <td><a href="{{ route('community.show', ['communityId' => $community->slug]) }}">{{ $community->slug }}</a></td>
                @else
                    <td><i>None</i></td>
                @endif
            </tr>
        </tbody>
    </table>

    <h3 class="ui header">@lang('pages.bars')</h3>
    @include('bar.include.list')
@endsection
