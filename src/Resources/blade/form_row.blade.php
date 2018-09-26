<tr>
    <td width="30%" style="text-align: right; padding: 15px">
        @if($item->isRequired())
            <span class="starrequired">*</span>
            <b>{{ $item->getLabel() }}: </b>
        @else
            {{ $item->getLabel() }}:
        @endif
    </td>
    <td width="70%">
        @yield('form_field')
        @php
        //var_dump($item->getLabel());
        @endphp
    </td>
</tr>