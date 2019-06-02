@extends('layouts.app')

@section('title', __('pages.payments.progress'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {{-- Payment steps banner --}}
    <div class="ui ordered unstackable steps tiny">
        <div class="active step">
            <div class="content">
                <div class="title">Transfer</div>
                <div class="description">Enter IBAN, transfer money</div>
            </div>
        </div>
        <div class="disabled step">
            <div class="content">
                <div class="title">Transfering</div>
                {{-- <div class="description">Wait for transfer</div> --}}
            </div>
        </div>
        <div class="disabled step">
            <div class="content">
                <div class="title">Receipt</div>
                {{-- <div class="description">Wait for confirmation</div> --}}
            </div>
        </div>
    </div>
    {{-- <div class="ui ordered unstackable steps tiny"> --}}
    {{--     <div class="completed step"> --}}
    {{--         <div class="content"> --}}
    {{--             <div class="title">Transfer</div> --}}
    {{--             {1{-- <div class="description">Enter IBAN, transfer money</div> --}1} --}}
    {{--         </div> --}}
    {{--     </div> --}}
    {{--     <div class="active step"> --}}
    {{--         <div class="content"> --}}
    {{--             <div class="title">Transfering</div> --}}
    {{--             <div class="description">Wait for transfer</div> --}}
    {{--         </div> --}}
    {{--     </div> --}}
    {{--     <div class="disabled step"> --}}
    {{--         <div class="content"> --}}
    {{--             <div class="title">Receipt</div> --}}
    {{--             {1{-- <div class="description">Wait for confirmation</div> --}1} --}}
    {{--         </div> --}}
    {{--     </div> --}}
    {{-- </div> --}}

    {{-- Embed payment step view --}}
    @include('barpay::payment.manualiban.stepTransfer')
@endsection
