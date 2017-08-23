{{ ErrorRenderer::alert(null, null, true) }}

@if(session('success'))
    <div class="alert alert-success">
        <span class="halflings halflings-ok-sign icon"></span> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <span class="halflings halflings-exclamation-sign icon"></span> {{ session('error') }}
    </div>
@endif