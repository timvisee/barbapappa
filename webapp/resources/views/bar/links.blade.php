@extends('layouts.app')

@section('title', __('pages.bar.links.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('bar.links', $bar);
    $menusection = 'bar_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.bar.links.description')</p>

    <table class="ui single line compact table">
        <thead>
            <tr><th colspan="3">@lang('misc.bar')</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>@lang('pages.bar.links.linkBar')</td>
                <td>
                    <button class="ui basic icon button mini compact share-button"
                            data-title="@lang('pages.bar.links.linkBarAction', ['bar' => $bar->name])"
                            data-url="{{ route('bar.show', ['barId' => $bar->human_id]) }}">
                        <i class="glyphicons glyphicons-share"></i>
                    </button>
                    <code class="literal copy">{{ route('bar.show', ['barId' => $bar->human_id]) }}</code>
                </td>
            </tr>
            @if($bar->self_enroll)
                <tr>
                    <td>@lang('pages.bar.links.linkJoinBar')</td>
                    <td>
                        <button class="ui basic icon button mini compact share-button"
                                data-title="@lang('pages.bar.links.linkJoinBarAction', ['bar' => $bar->name])"
                                data-url="{{ route('bar.show', ['barId' => $bar->human_id]) }}">
                            <i class="glyphicons glyphicons-share"></i>
                        </button>
                        <code class="literal copy">{{ route('bar.join', ['barId' => $bar->human_id, 'code' => $bar->password]) }}</code>
                    </td>
                </tr>
            @endif
            @if($bar->self_enroll && $bar->password)
                <tr>
                    <td>@lang('pages.bar.links.linkJoinBarCode')</td>
                    <td>
                        <button class="ui basic icon button mini compact share-button"
                                data-title="@lang('pages.bar.links.linkJoinBarCodeAction', ['bar' => $bar->name])"
                                data-url="{{ route('bar.join', ['barId' => $bar->human_id, 'code' => $bar->password]) }}">
                            <i class="glyphicons glyphicons-share"></i>
                        </button>
                        <code class="literal copy">{{ route('bar.join', ['barId' => $bar->human_id, 'code' => $bar->password]) }}</code>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <table class="ui single line compact table">
        <thead>
            <tr><th colspan="3">@lang('misc.user') (@lang('misc.personal'))</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>@lang('pages.bar.links.linkQuickWallet')</td>
                <td>
                    <button class="ui basic icon button mini compact share-button"
                            data-title="@lang('pages.bar.links.linkQuickWalletAction')"
                            data-url="{{ route('community.wallet.quickShow', ['communityId' => $community->human_id, 'economyId' => $bar->economy_id]) }}">
                        <i class="glyphicons glyphicons-share"></i>
                    </button>
                    <code class="literal copy">
                        {{ route('community.wallet.quickShow', ['communityId' => $community->human_id, 'economyId' => $bar->economy_id]) }}
                    </code>
                </td>
            </tr>
            <tr>
                <td>@lang('pages.bar.links.linkQuickTopUp')</td>
                <td>
                    <button class="ui basic icon button mini compact share-button"
                            data-title="@lang('pages.bar.links.linkQuickTopUpAction')"
                            data-url="{{ route('community.wallet.quickTopUp', ['communityId' => $community->human_id, 'economyId' => $bar->economy_id]) }}">
                        <i class="glyphicons glyphicons-share"></i>
                    </button>
                    <code class="literal copy">
                        {{ route('community.wallet.quickTopUp', ['communityId' => $community->human_id, 'economyId' => $bar->economy_id]) }}
                    </code>
                </td>
            </tr>
            <tr>
                <td>@lang('pages.bar.links.linkQuickTopUpRedemption')</td>
                <td>
                    <button class="ui basic icon button mini compact share-button"
                            data-title="@lang('pages.bar.links.linkQuickTopUpRedemptionAction')"
                            data-url="{{ route('community.wallet.quickTopUp', ['communityId' => $community->human_id, 'economyId' => $bar->economy_id, 'redemption' => 1]) }}">
                        <i class="glyphicons glyphicons-share"></i>
                    </button>
                    <code class="literal copy">
                        {{ route('community.wallet.quickTopUp', ['communityId' => $community->human_id, 'economyId' => $bar->economy_id, 'redemption' => 1]) }}
                    </code>
                </td>
            </tr>
            <tr>
                <td>@lang('pages.bar.links.linkVerifyEmail')</td>
                <td>
                    <button class="ui basic icon button mini compact share-button"
                            data-title="@lang('pages.bar.links.linkVerifyEmailAction')"
                            data-url="{{ route('account.user.emails.unverified', ['userId' => '-']) }}">
                        <i class="glyphicons glyphicons-share"></i>
                    </button>
                    <code class="literal copy">
                        {{ route('account.user.emails.unverified', ['userId' => '-']) }}
                    </code>
                </td>
            </tr>
        </tbody>
    </table>

    <a href="{{ route('bar.manage', ['barId' => $bar->human_id]) }}"
            class="ui button basic">
        @lang('pages.bar.backToBar')
    </a>
@endsection
