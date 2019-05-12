{{ Illuminate\Mail\Markdown::parse($slot) }},

@if(isset($lead))
{{ Illuminate\Mail\Markdown::parse($lead) }}
@endif