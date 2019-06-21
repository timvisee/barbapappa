{{-- TODO: implement pagination --}}

@foreach($groups as $group)
    {{-- Header --}}
    @if(isset($group['header']))
        {{-- TODO: headers --}}
        <h3 class="ui item header">
            {{ $group['header'] }}
        </h3>
    @endif

    {{-- Notifications --}}
    <div class="ui three stackable cards">
        @forelse($group['notifications'] as $notification)
            @php
                $data = $notification->viewData();
            @endphp

            <div class="ui card {{ $group['cardClass'] ?? '' }}">
                <div class="content">
                    {{-- <div class="header">Cute Dog</div> --}}
                    <div class="meta">
                        <div class="right floated time">
                            @include('includes.humanTimeDiff', ['time' => $notification->updated_at])
                        </div>
                        <div class="context">
                            {{ $data['kind'] }}
                        </div>
                    </div>
                    <div class="description">
                        <p>{{ $data['message'] }}</p>
                    </div>
                </div>

                <div class="extra content">
                    @if(isset($data['actions']))
                        @foreach($data['actions'] as $action)
                            @if(!isset($group['disabled']) || !$group['disabled'])
                                <a href="{{ route('notification.action', [
                                    'notificationId' => $notification->id,
                                    'action' => $action['action'],
                                ]) }}"
                                class="ui compact tiny button primary basic">
                                    {{ $action['label'] }}
                                </a>
                            @else
                                <a href="{{ route('notification.action', [
                                    'notificationId' => $notification->id,
                                    'action' => $action['action'],
                                ]) }}"
                                class="ui compact tiny button basic">
                                    {{ $action['label'] }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        @empty
            <i class="item">@lang('pages.notification.noNotifications')</i>
        @endforelse
    </div>
@endforeach
