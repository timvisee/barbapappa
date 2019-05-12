@include('mail.template.markdown.header', ['subject' => $subject])

{!! strip_tags($slot) !!}

@include('mail.template.markdown.footer')

