@extends('layouts.app')

@section('title', __('pages.balanceImportMailBalance.title'))
@php
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>@lang('pages.balanceImportMailBalance.description')</p>

    <div class="ui hidden divider"></div>

    {!! Form::open([
        'action' => [
            'BalanceImportSystemController@doMailBalance',
            $community->human_id,
            $economy->id,
            $system->id,
        ],
        'method' => 'POST',
        'class' => 'ui form'
    ]) !!}
        <div class="inline field {{ ErrorRenderer::hasError('mail_unregistered_users') ?  'error' : '' }}">
            <div class="ui toggle checkbox">
                {{ Form::checkbox('mail_unregistered_users', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('mail_unregistered_users', __('pages.balanceImportMailBalance.mailUnregisteredUsers')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('mail_unregistered_users') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('mail_not_joined_users') ?  'error' : '' }}">
            <div class="ui toggle checkbox">
                {{ Form::checkbox('mail_not_joined_users', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('mail_not_joined_users', __('pages.balanceImportMailBalance.mailNotJoinedUsers')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('mail_not_joined_users') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('mail_joined_users') ?  'error' : '' }}">
            <div class="ui toggle checkbox">
                {{ Form::checkbox('mail_joined_users', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('mail_joined_users', __('pages.balanceImportMailBalance.mailJoinedUsers')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('mail_joined_users') }}
        </div>

        <div class="ui hidden divider"></div>

        <div class="inline field {{ ErrorRenderer::hasError('mail_joined_users') ?  'error' : '' }}">
            <div class="ui toggle checkbox">
                {{ Form::checkbox('limit_last_event', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('limit_last_event', __('pages.balanceImportMailBalance.limitToLastEvent') . ': ' . ($last_event != null ? $last_event->name : '?')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('limit_last_event') }}
        </div>

        <div class="ui hidden divider"></div>

        <div class="field {{ ErrorRenderer::hasError('message') ? 'error' : '' }}">
            {{ Form::label('message', __('pages.balanceImportMailBalance.extraMessage') . ' :') }}
            {{ Form::textarea('message', '', ['rows' => 3]) }}
            {{ ErrorRenderer::inline('message') }}
        </div>

        <div class="ui hidden divider"></div>

        <div class="field {{ ErrorRenderer::hasError('related_bar') ? 'error' : '' }}">
            {{ Form::label('related_bar', __('pages.balanceImportMailBalance.relatedBar') . ' (' . __('general.recommended') . '):') }}

            <div class="ui fluid selection dropdown">
                {{ Form::hidden('related_bar', $community->bars->pluck('id')->first() ?? 0) }}
                <i class="dropdown icon"></i>

                <div class="default text">@lang('misc.pleaseSpecify')</div>
                <div class="menu">
                    <div class="item" data-value="0">
                        <i>@lang('pages.balanceImportMailBalance.noRelatedBar')...</i>
                    </div>
                    {{-- TODO: only select joinable bars here --}}
                    @foreach($community->bars as $bar)
                        <div class="item" data-value="{{ $bar->id }}">
                            {{ $bar->name }}
                        </div>
                    @endforeach
                </div>
            </div>

            {{ ErrorRenderer::inline('related_bar') }}
        </div>

        <div class="field {{ ErrorRenderer::hasError('invite_to_bar') ? 'error' : '' }}">
            <div class="ui toggle checkbox">
                {{ Form::checkbox('invite_to_bar', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('invite_to_bar', __('pages.balanceImportMailBalance.inviteToJoinBar') . ' (' . __('general.recommended') . ')') }}
            </div>
            <br />
            {{ ErrorRenderer::inline('invite_to_bar') }}
        </div>

        <div class="ui hidden divider"></div>

        @php
            // Create a locales map for the selection box
            $locales = [];
            foreach(langManager()->getLocales(true, false) as $entry)
                $locales[$entry] = __('lang.name', [], $entry);
        @endphp

        <div class="field {{ ErrorRenderer::hasError('language') ? 'error' : '' }}">
            {{ Form::label('language', __('lang.language') . ':') }}

            <div class="ui fluid selection dropdown">
                {{ Form::hidden('language', langManager()->getLocale()) }}
                <i class="dropdown icon"></i>

                <div class="default text">@lang('misc.unspecified')</div>
                <div class="menu">
                    @foreach($locales as $locale => $name)
                        <div class="item" data-value="{{ $locale }}">
                            <span class="{{ langManager()->getLocaleFlagClass($locale, false, true) }} flag"></span>
                            {{ $name }}
                        </div>
                    @endforeach
                </div>
            </div>

            {{ ErrorRenderer::inline('language') }}
        </div>

        <div class="field {{ ErrorRenderer::hasError('reply_to') ? 'error' : '' }}">
            {{ Form::label('reply_to', __('pages.balanceImportMailBalance.replyToAddress') . ':') }}
            {{ Form::text('reply_to', $email ?? '', ['type' => 'reply_to', 'placeholder' => __('account.emailPlaceholder')]) }}
            {{ ErrorRenderer::inline('reply_to') }}
        </div>

        <div class="ui hidden divider"></div>

        <div class="ui divider"></div>

        {{-- Mail send confirmation checkbox --}}
        <div class="required field {{ ErrorRenderer::hasError('confirm_send_mail') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('confirm_send_mail', true, false, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('confirm_send_mail', __('pages.balanceImportMailBalance.confirmSendMessage')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('confirm_send_mail') }}
        </div>

        <br>

        <button class="ui button primary" type="submit" name="submit" value="">
            @lang('misc.send')
        </button>
        <a href="{{ route('community.economy.balanceimport.event.index', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'systemId' => $system->id,
                ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
