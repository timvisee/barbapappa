@if(isset($isOtherUser) && $isOtherUser)
    <div class="ui warning message">
        <span class="halflings halflings-warning-sign icon"></span>
        @lang('misc.viewingAccountOf'): {{ $user->name }}
    </div>
@endif
