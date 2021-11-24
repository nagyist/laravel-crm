<table>
    <thead>
        <tr>
            @foreach ($columns as $key => $value)
                @php
                    $title =  $value == 'increment_id' ? 'order_id' : $value;
                @endphp

                <th>{{ $title }}</th>
            @endforeach
        </tr>
    </thead>
    
    <tbody>
        @foreach ($records as $record)
            <tr>
                @foreach($record as $column => $value)
                    @php
                        $value = preg_replace('/[^A-Za-z0-9@#$%^&*()_!+\-]/', '', $value);
                    @endphp

                    <td> {{ $value }} </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>