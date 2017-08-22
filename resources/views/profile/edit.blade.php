@extends('layouts.app')

@section('content')
    <h1>@lang('pages.editProfile')</h1>

    {!! Form::open(['action' => ['ProfileController@update', $user->id], 'method' => 'POST']) !!}

        {{ Form::label('first_name', __('account.firstName')) }}
        {{ Form::text('first_name', $user->first_name) }}
        <br />

        {{ Form::label('last_name', __('account.lastName')) }}
        {{ Form::text('last_name', $user->last_name) }}
        <br />

        <?php
            // Create a locales map for the selection box
            $locales = Array(
                '' => '- ' . __('misc.unspecified') . ' -'
            );
            foreach(langManager()->getLocales(true, false) as $entry)
                $locales[$entry] = __('lang.name', [], $entry);
        ?>

        {{ Form::label('locale', __('lang.language')) }}
        {{ Form::select('locale', $locales, $user->locale) }}
        <br />

        {{ Form::hidden('_method', 'PUT') }}

        {{ Form::submit(__('misc.saveChanges')) }}

    {!! Form::close() !!}
@endsection
