@extends('form_row')
@section('form_field')
    <input size="47" type="text" name="{{ $name }}" class="{{ $styleClass }} calendar_once" value="{{ $value }}" />
@endsection