{{ ErrorRenderer::alert(null, null, true) }}

@if(session('error') || session('errorHtml'))
    <div class="ui error message">
        <span class="halflings halflings-exclamation-sign icon"></span>
        @if(session('errorHtml'))
            {!! session('errorHtml') !!}
        @else
            {{ session('error') }}
        @endif
    </div>
@endif

@if(session('success') || session('successHtml'))
    <div class="ui success message">
        <span class="halflings halflings-ok-sign icon"></span>
        @if(session('successHtml'))
            {!! session('successHtml') !!}
        @else
            {{ session('success') }}
        @endif
    </div>
@endif

@if(session('info') || session('infoHtml'))
    <div class="ui info message">
        <span class="halflings halflings-info-sign icon"></span>
        @if(session('infoHtml'))
            {!! session('infoHtml') !!}
        @else
            {{ session('info') }}
        @endif
    </div>
@endif
