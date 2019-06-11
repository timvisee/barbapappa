@extends('layouts.app')

@section('title', __('pages.bunqAccounts.title'))

{{-- TODO: define menulinks! --}}
{{-- @php --}}
{{--     // Define menulinks --}}
{{--     $menulinks[] = [ --}}
{{--         'name' => __('pages.community.goTo'), --}}
{{--         'link' => route('community.show', ['communityId' => $community->human_id]), --}}
{{--         'icon' => 'undo', --}}
{{--     ]; --}}
{{--     $menulinks[] = [ --}}
{{--         'name' => __('pages.wallets.all'), --}}
{{--         'link' => route('community.wallet.index', ['communityId' => $community->human_id]), --}}
{{--         'icon' => 'wallet', --}}
{{--     ]; --}}
{{-- @endphp --}}

@section('content')
    <h2 class="ui header">
        @yield('title') ({{ count($accounts) }})

        {{-- TODO: use breadcrumbs here --}}
        {{-- <div class="sub header"> --}}
        {{--     @lang('misc.in') --}}
        {{--     <a href="{{ route('community.wallet.index', ['communityId' => $community->human_id]) }}"> --}}
        {{--         {{ $community->name }} --}}
        {{--     </a> --}}
        {{--     @lang('misc.for') --}}
        {{--     {{ $economy->name }} --}}
        {{-- </div> --}}
    </h2>

    {{-- TODO: change translation --}}
    <p>@lang('pages.bunqAccounts.description')</p>

    <div class="ui vertical menu fluid">
        {{--
            <div class="item">
                <div class="ui transparent icon input">
                    {{ Form::text('search', '', ['placeholder' => 'Search communities...']) }}
                    <i class="icon glyphicons glyphicons-search link"></i>
                </div>
            </div>
        --}}

        @forelse($accounts as $account)
            {{-- TODO: link to bunq account page --}}
            <a href="{{ route('community.bunqAccount.show', [
                'communityId' => $community->human_id,
                'accountId' => $account->id
            ]) }}" class="item">
                TODO: some wallet name
            </a>
        @empty
            <div class="item">
                {{-- TODO: translate --}}
                <i>@lang('pages.bunqAccounts.noAccounts')</i>
            </div>
        @endforelse
    </div>

    {{-- TODO: check whether user can add new bunq account --}}
    <a href="{{ route('community.bunqAccount.create', ['communityId' => $community->human_id]) }}"
            class="ui button basic positive">
        @lang('misc.add')
    </a>

    <a href="{{ route('community.manage', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('pages.community.backToCommunity')
    </a>
@endsection
