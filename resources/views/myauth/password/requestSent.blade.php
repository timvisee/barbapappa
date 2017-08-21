@extends('layouts.app')

@section('content')

    <h1>@lang('pages.passwordRequestSent.title')</h1>
    <p>@lang('pages.passwordRequestSent.message', ['hours' => 24])</p>

@endsection
