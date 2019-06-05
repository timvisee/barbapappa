@foreach($table as $row)
{{ isset($row['keyHtml']) ? strip_tags($row['keyHtml']) : $row['key'] }}: {{ isset($row['valueHtml']) ? strip_tags($row['valueHtml']) : $row['value'] }}
@endforeach
