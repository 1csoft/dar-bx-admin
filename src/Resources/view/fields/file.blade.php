@extends('form_row')
@section('form_field')
    @php
        echo \Bitrix\Main\UI\FileInput::createInstance(array(
			"name" => $item->getName(),
			"description" => true,
			"upload" => true,
			"allowUpload" => "I",
			"medialib" => true,
			"fileDialog" => true,
			"cloud" => true,
			"delete" => true,
			"maxCount" => 1
		))->show();
    @endphp
@endsection;
