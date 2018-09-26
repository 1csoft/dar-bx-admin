@extends('form_row')
@section('form_field')
    <input type="hidden" name="{{ $name }}" value="{{ $value }}"/>
    <p class="{{ $styleClass }}">{{ $value }}</p>
@endsection