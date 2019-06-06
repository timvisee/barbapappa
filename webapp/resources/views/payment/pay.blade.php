@extends('layouts.app')

@section('title', __('pages.payments.progress'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {{-- Payment steps banner --}}
    <div class="ui ordered unstackable steps tiny">
        @foreach($steps as $step)
            @php
                switch($step['state'] ?? null) {
                    case 1:
                        $class = 'completed';
                        break;
                    case 0:
                        $class = 'active';
                        break;
                    case -1:
                        $class = 'disabled';
                        break;
                }
            @endphp
            <div class="step {{ $class }}">
                <div class="content">
                    <div class="title">{{ $step['label'] }}</div>
                    @if(isset($step['description']))
                        <div class="description">{{ $step['description'] }}</div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Embed payment step view --}}
    @include($stepView)
@endsection
