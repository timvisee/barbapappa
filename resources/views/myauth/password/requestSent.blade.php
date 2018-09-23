@extends('layouts.app')

@section('content')

    <h2 class="ui header">@lang('pages.passwordRequestSent.title')</h2>
    <p>@lang('pages.passwordRequestSent.message', ['hours' => 24])</p>

@endsection
