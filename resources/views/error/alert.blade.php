@foreach($messages as $message)
    <div class="alert alert-danger">
        {{ $message }}
    </div>
@endforeach
