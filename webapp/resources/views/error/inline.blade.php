@foreach($messages as $message)
    <div class="ui pointing red basic label">
        <span class="halflings halflings-exclamation-sign icon"></span>
        {{ $message }}
    </div>
@endforeach
