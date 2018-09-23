{{ ErrorRenderer::alert(null, null, true) }}

@if(session('success'))
    <div class="ui success message">
        <span class="halflings halflings-ok-sign icon"></span>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="ui error message">
        <span class="halflings halflings-exclamation-sign icon"></span>
        {{ session('error') }}
    </div>
@endif
