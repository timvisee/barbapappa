@extends('layouts.app')

@section('title', $community->name)

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.community.deleteQuestion')</p>

    <div class="ui top attached warning message visible">
        <span class="halflings halflings-warning-sign"></span>
        @lang('misc.cannotBeUndone')
    </div>

    {!! Form::open(['action' => ['CommunityController@doDelete', 'communityId' => $community->human_id], 'method' => 'DELETE', 'class' => 'ui form']) !!}
        <div class="ui attached segment">
            <div class="field {{ ErrorRenderer::hasError('confirm_name') ? 'error' : '' }}">
                {{ Form::hidden('confirm_name_base', $community->name) }}
                {{ Form::label('confirm_name', __('pages.community.exactCommunityNameVerify') . ':') }}
                {{ Form::text('confirm_name', '', ['placeholder' => $community->name]) }}
                {{ ErrorRenderer::inline('confirm_name') }}
                <br />
                {{ ErrorRenderer::inline('confirm_name') }}
            </div>
        </div>

        {{-- Delete confirmation checkbox --}}
        <div class="ui bottom attached segment">
            <div class="field {{ ErrorRenderer::hasError('confirm_delete') ? 'error' : '' }}">
                <div class="ui checkbox">
                    <input type="checkbox"
                            name="confirm_delete"
                            tabindex="0"
                            class="hidden">
                    {{ Form::label('confirm_delete', __('misc.iUnderstandDelete')) }}
                </div>
                <br />
                {{ ErrorRenderer::inline('confirm_delete') }}
            </div>
        </div>

        <br />

        <div class="ui buttons">
            <a href="{{ route('community.manage', ['communityId' => $community->human_id]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesRemove')</button>
        </div>
    {!! Form::close() !!}
@endsection
