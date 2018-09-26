@extends('form_row')
@section('form_field')
    <textarea name="{{ $name }}" class="{{ $styleClass }}"
            cols="{{ $options['cols'] }}" rows="{{ $options['rows'] }}">{{ $value }}</textarea>
@endsection