<table class="details">
    @foreach($table as $row)
        <tr>
            @if(isset($row['keyHtml']))
                <td><b>{!! $row['keyHtml'] !!}</b></td>
            @else
                <td><b>{{ $row['key'] }}</b></td>
            @endif
            @if(isset($row['valueHtml']))
                <td>{!! $row['valueHtml'] !!}</td>
            @else
                <td>{{ $row['value'] }}</td>
            @endif
        </tr>
    @endforeach
</table>
