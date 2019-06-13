@php
    use App\Http\Controllers\AppBunqAccountController;
    use App\Models\BunqAccount;
@endphp

<div class="field {{ ErrorRenderer::hasError('bunq_account') ? 'error' : '' }}">
    {{ Form::label('bunq_account', __('pages.bunqAccounts.bunqAccount') . ':') }}

    <div class="ui fluid selection dropdown">
        {{ Form::hidden('bunq_account', null) }}
        <i class="dropdown icon"></i>

        <div class="default text">@lang('misc.pleaseSpecify')</div>
        <div class="menu">
            {{-- TODO: only select allowed bunq accounts here --}}
            @foreach(BunqAccount::all() as $account)
                <div class="item" data-value="{{ $account->id }}">
                    {{ $account->name }}
                </div>
            @endforeach
        </div>
    </div>

    {{ ErrorRenderer::inline('bunq_account') }}
</div>

@if(perms(AppBunqAccountController::permsView()))
    <a href="{{ route('community.bunqAccount.index', ['communityId' => $community->human_id]) }}">
        @lang('pages.bunqAccounts.manageCommunityAccounts')
    </a>
@endif

@if(perms(AppBunqAccountController::permsView()))
    <br>
    <a href="{{ route('app.bunqAccount.index') }}">
        @lang('pages.bunqAccounts.manageAppAccounts')
    </a>
@endif
