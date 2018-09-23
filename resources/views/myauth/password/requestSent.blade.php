@extends('layouts.app')

@section('content')

    <h3 class="ui header">@lang('pages.passwordRequestSent.title')</h3>
    <p>@lang('pages.passwordRequestSent.message', ['hours' => 24])</p>

@endsection
