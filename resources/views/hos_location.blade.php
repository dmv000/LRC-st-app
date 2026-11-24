@extends('layouts.master')
@section('content')

<link rel = "stylesheet" href = "/leaflet/leaflet-0.7.3.css"/>
<script src = "/leaflet/leaflet-0.7.3.js"></script>

<style>
      #map_new {
       height: 600px;
       width: 100%;
      }
   </style>

   <div class="right_col" role="main">
       <div class="page-title">
           <div class="title_left">
               <h3>{{ $hospital_name }}</h3>
           </div>
       </div>
       <hr width=100%>
       <div class="row">

           <div class="col-md-6 col-sm-6 col-xs-12" style="margin-left: 25%;margin-right: 25%;">
               <div id="map_new" ></div>
               <br/>
               <hr style="width:100%">
               <br/>
               <div style="font-size:20px;">
                   Description :
                   <ul>
                       <li>District : {{ $district }}</li>
                       <li>Station : {{ $station }}</li>
                       <li id="phone">Phone Number<span id="many">s</span> :
                           <ul>
                           @foreach($phones as $phone)
                                <li>{{$phone->phone}}</li>
                           @endforeach
                           </ul>
                       </li>
                   </ul>
               </div>
           </div>
       </div>
   </div>

<script>

    $(function(){
        if({{count($phones)}} == 0){
            $('#phone').hide();
        }else if({{count($phones)}} == 1){
            $('#many').hide();
        }
    });
	
	var mapOptions = {
		center: [{{ $lat }},{{ $lon }}],
		zoom: 15
	};
                 
	// Creating a map object
	var map = new L.map('map_new', mapOptions);

	// Creating a Layer object
	var layer = new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');

	// Adding layer to the map
	map.addLayer(layer);

</script>

@endsection
