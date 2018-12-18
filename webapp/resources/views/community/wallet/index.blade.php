@extends('layouts.app')

@section('content')
    <h2 class="ui header">
        @lang('pages.wallets.walletEconomies')
        <div class="sub header">
            in
            <a href="{{ route('community.show', ['communityId' => $community->id]) }}">
                {{ $community->name }}
            </a>
        </div>
    </h2>
    <p>@lang('pages.wallets.economySelectDescription')</p>

    <div class="ui vertical menu fluid">
        {{--
            <div class="item">
                <div class="ui transparent icon input">
                    <input type="text" placeholder="Search communities...">
                    <i class="icon glyphicons glyphicons-search link"></i>
                </div>
            </div>
        --}}

        @forelse($economies as $economy)
            <a href="{{ route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" class="item">
                {{ $economy->name }}
                {!! $economy->formatBalance(BALANCE_FORMAT_LABEL) !!}
            </a>
        @empty
            <div class="item">
                <i>@lang('pages.economies.noEconomies')</i>
            </div>
        @endforelse
    </div>

    <a href="{{ route('community.show', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('pages.community.goTo')
    </a>
@endsection
