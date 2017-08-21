@extends('layouts.app')

@section('content')
    <h1>{{ $title }}</h1>
    <p>Index page.</p>

    <hr />

    @lang('general.test')

    <br>
    <br>

    {{ trans_random('general.test', ['a' => 'SOMETHING']) }}

    <hr />

    <table>
        <tr>
            <td>Auth:</td>
            <td>{{ $auth }}</td>
        </tr>
        <tr>
            <td>Verified:</td>
            <td>{{ $verified }}</td>
        </tr>
    </table>
@endsection
