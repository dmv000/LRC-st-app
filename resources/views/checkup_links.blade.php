@extends('layouts.master')
@section('content')

<style>
    .error{
        color:red;
        font-family: Cambira;
    }

    .error_td{
        padding-left:10px;
    }

    .error_h2{
        color:red;
    }
</style>
{!! QrCode::size(200)->generate('http://192.168.30.34/x/#'); !!}
{!!QrCode::size(200)->wiFi([
    'ssid' => 'Network Name',
    'encryption' => 'WPA',
    'password' => 'myPassword'
]);!!}
@endsection
