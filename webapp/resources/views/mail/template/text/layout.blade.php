@include('mail.template.text.header', ['subject' => $subject])

{!! strip_tags($slot) !!}

@include('mail.template.text.footer')

