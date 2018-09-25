@extends('form_row')
@section('form_field')
    <select name="{{ $name }}" class="{{ $styleClass }}">
        @foreach($items as $option)
            @if($option == $value)
                <option value="{{ $option }}" selected>{{ $option }} </option>
            @else
                <option value="{{ $option }}">{{ $option }} </option>
            @endif
        @endforeach
    </select>
@endsection