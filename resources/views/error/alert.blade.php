@foreach($messages as $message)
    <div class="alert alert-danger">
        <span class="halflings halflings-exclamation-sign icon"></span> {{ $message }}
    </div>
@endforeach
