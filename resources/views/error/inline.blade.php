@foreach($messages as $message)
    <div class="ui error message small visible">
        <span class="halflings halflings-exclamation-sign icon"></span>
        {{ $message }}
    </div>
@endforeach
