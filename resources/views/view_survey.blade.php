<html>
<head>
<title>Investigate Survey</title>
<style>
textarea {
	width: 200px;
	min-width: 200px;
	max-width: 200px;	
}
</style>
</head>
<body>

<div data-role="page" data-type="page" data-hash="00000006" data-type-id="90001" data-colspan="20" data-num-children="9" data-page-index="0" data-is-first-page="1" data-is-current-page="1" data-is-last-page="yes">
	<div data-role="page-header">
		<div data-role="control" data-type="form-header" data-hash="00000002" data-type-id="27" data-colspan="20" id="form-heading-00000002-acc" aria-labelledby="form-heading-00000002-acc" data-i18n-html="Headline">
			<h1>Driver Assessment Survey</h1>
			<h2>Driver : {{ $driver->name }} ({{ $driver->nickname }})</h2>
			<h2>EMT : {{ $emt->nickname }}</h2>
			<h2>Ambulance : {{ $ambulance }}</h2>
			<h2>Departure : {{ $departure }}</h2>
			<h2>Patient Name : {{ $patient }}</h2>
		</div>
	</div>
	<hr/>
<form action="driver_survey">
	<fieldset>
		<legend>
			Information
		</legend>
<input name="guid" value="{{$guid}}" style="display: none"/>
@foreach($fields as $field)
@if($field->type == "Bool")
<span>{{$field->arabic_description}}</span><br/>
<input disabled type="radio" onchange="toggle_val({{$field->name}},{{$field->default}})" name="{{$field->name}}" @if($field->value == "true") checked @endif value="true">Yes</input>
<input disabled type="radio" onchange="toggle_val({{$field->name}},{{$field->default}})" name="{{$field->name}}" @if($field->value == "false") checked @endif value="false">No</input>
<br/>
<textarea disabled name="comment_{{ $field->name }}">{{ $field->comment }}</textarea>
<hr/>
@elseif($field->type == "Text")
<span>{{$field->arabic_description}}</span><br/>
<textarea disabled name="{{$field->name}}">{{ $field->value }}</textarea>
<hr/>
@endif
@endforeach
</fieldset>
</form>
</body>
</html>