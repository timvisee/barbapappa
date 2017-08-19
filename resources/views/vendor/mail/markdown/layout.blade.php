@include('vendor.mail.markdown.header', ['subject' => $subject])

{!! strip_tags($slot) !!}

@include('vendor.mail.markdown.footer')

