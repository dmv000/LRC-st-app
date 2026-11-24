@extends('layouts.master')
@section('content')

<link href="css/select2.css" rel="stylesheet" />
<script src="js/select2.js"></script>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3></h3>
        </div>
    </div>
    <hr width=100%>
	@if(Auth::check() && Auth::user()->memberOf('Secretariat Members')) 
	<div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Hospitals</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
					<button type="button" onclick="addHospital()" class="btn btn-primary">Add Hospital</button>
				</div>
			</div>
		</div>
	</div>
	@endif
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Hospitals</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <form class="form-vertical form-label-left">
                    <div class="form-group">
                        <label style="margin-top:9px;" class="control-label col-md-1 col-sm-1 col-xs-1">District</label>
                        <div class="col-md-2 col-sm-2 col-xs-2">
                          <select id="district_id" onchange="filterrrr()" class="select2_single form-control" tabindex="-1">
                            <option value="-1"></option>
                            @foreach($districts as $district)
                            <option value="{{ $district->id }}">{{ $district->name . " (" . $district->id . ")" }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="form-group">
                        <label style="margin-top:9px;" class="control-label col-md-1 col-sm-1 col-xs-1">Station</label>
                        <div class="col-md-2 col-sm-2 col-xs-2">
                          <select id="station_id" onchange="filterrrr2()" class="select2_single form-control" tabindex="-1">
                            <option value="-1"></option>
                          </select>
                        </div>
                      </div>
                  </form>
                  <br/>
                    <table id="example" class="display" style="color:black" width="100%"></table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="staticModal" tabindex="-1" role="dialog" aria-labelledby="staticModalLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="staticModalLabel">Manage Contacts</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<input type="text" id="manageHospitalId" name="manageHospitalId" value="" style="display: none">
			<div class="modal-body" id="modal_bd"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="button" onclick="saveContacts()" class="btn btn-primary">Save</button>
				<button type="button" onclick="addContactModal()" class="btn btn-primary">Add Contact</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="hospitalModal" tabindex="-1" role="dialog" aria-labelledby="hospitalModalLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="hospitalModalLabel">Add Hospital</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="add_modal_bd">
				<div class="form-group">
					<div class="col col-md-2">
						<label for="hospitalDistrict" class="no-edit form-control-label">District</label>
					</div>
					<div class="col col-md-4">
						<select class="form-control standardSelect" name="hospitalDistrict" id="newHospitalDistrict" style="display:none;" tabindex="-1">
							<option value="-1"></option>
							@foreach($districts as $district)
							<option value="{{ $district->id }}">{{ $district->name . " (" . $district->id . ")" }}</option>
							@endforeach
						</select>
					</div>
					<div class="col col-md-2">
						<label for="hospitalStation" class="no-edit form-control-label">Station</label>
					</div>
					<div class="col col-md-4">
						<select class="form-control standardSelect" name="hospitalStation" id="newHospitalStation" style="display:none;" tabindex="-1">
							<option value="-1"></option>
						</select>
					</div>
				</div>
				<br>
				<br>
				<div class="form-group">
					<div class="col col-md-2">
						<label for="hospitalName" class="no-edit form-control-label">Hospital Name</label>
					</div>
					<div class="col col-md-4">
						<input type="text" id="newHospitalName" name="hospialName" class="form-control">
					</div>
					<div class="col col-md-2">
						<label for="hospitalCode" class="no-edit form-control-label">Code</label>
					</div>
					<div class="col col-md-4">
						<input type="text" id="newHospitalCode" name="hospitalCode" class="form-control">
					</div>
				</div>
				<br>
				<br>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="button" onclick="saveHospital()" class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</div>




<script src="../build/js/jquery.dataTables.min.js"></script>
<script>

function addHospital() {
	$('#hospitalModal').modal();
}

function saveHospital() {
	try {
		let hospitalDetails = getNewHospitalDetails();
		saveHositalDetails(hospitalDetails);
	} catch(err) {
		showGlobalError(err.message);
	}
}

function saveHositalDetails(details) {
	sendPostRequest({
		url : 'hospitals',
		data : details
	}).then(response => {
		if(response.Status == 1) {
			showGlobalSuccess(response.Message + '<br/>Refresh page to view Changes');
			$('#hospitalModal').modal('hide');
			resetAddHospital();
		} else {
			showGlobalError(response.Message);
		}
		console.log('Success', response);
	}).catch(err => {
		showGlobalError('Failed adding new Hospital. Please contact IT');
	});
}

function resetAddHospital() {
	$('#newHospitalDistrict').val('-1');
	$('#newHospitalDistrict').trigger('change');
	$('#newHospitalStation').val('-1');
	$('#newHospitalStation').trigger('change');
	$('#newHospitalName').val('');
	$('#newHospitalCode').val('');
	$('#newHospitalStation').empty();
	$('#newHospitalStation').append('<option value="-1"></option>');
}

function getNewHospitalDetails() {
	const district = $('#newHospitalDistrict').select2('data');
	let districtId = -1;
	if(district) {
		districtId = district[0]['id'];
	} 
	if(districtId == -1) {
		throw new Error('District is required');
	}
	const station = $('#newHospitalStation').select2('data');
	let stationId = -1;
	if(station) {
		stationId = station[0]['id'];
	} 
	if(stationId == -1) {
		throw new Error('Station is required');
	}
	let hospitalName = $('#newHospitalName').val();
	if(!hospitalName || hospitalName === "") {
		throw new Error('Hospital Name is required');
	}
	let hospitalCode = $('#newHospitalCode').val();

	return {
		districtId: districtId,
		stationId: stationId,
		hospitalName: hospitalName,
		hospitalCode: hospitalCode
	};
}

function saveContacts(){
	var hosId = $('#manageHospitalId').val();
	var contacts = [];
	$('.contactRow').each(function(index,row){
		var contact = {};
		contact.id=$($(row).find('.manageContactId')[0]).val();
		contact.name=$($(row).find('.manageContactValue')[0]).val();
		contacts[index] = contact;
	});
	
	var data = {};
	data['hospitalId'] = hosId;
	data['contacts'] = contacts;
	
	var formData = {
			data : data,
			"_token": "{{ csrf_token() }}"
		} 

	jQuery.ajax({
		url:'update_hospital_contacts',
		dataType:'json',
		type: 'post',
		data: formData,
		success:function(response){
			alert(response.Message);
			$('#staticModal').modal('hide');
		},error:function(){
			alert('Failed updating Contacts. Please consult IT support');
		}
	});
}

function addContactModal(id,contac){
	
	id = id==undefined?0:id;
	contac = contac==undefined?'':contac;
	var str = '<div class="row form-group contactRow">'+
						'<input type="text" name="manageContactId" value="'+id+'" style="display: none" class="manageContactId">'+
						'<div class="col col-md-3"><label for="text-input" class="no-edit form-control-label">Contact</label></div>'+
						'<div class="col-12 col-md-7"><input value="'+contac+'" type="text" name="manageContactValue" placeholder="Contact" class="form-control manageContactValue"></div>'+
						'<div class="col col-md-2"><button type="button" onclick="removeContact(this)" class="btn btn-danger">Remove</button></div>'+
					'</div>';
	$($.parseHTML(str)).appendTo( $('#modal_bd'));
}

function removeContact(e){
	var row = $(e).parent().parent().remove();
}

var newId = 500;
var newDistrictId = {{ env('STATION_NUMBER') }};

function filterrrr(){
    newId = $('#district_id').val();
    if(newId != -1){
        dataSet2 = dataSet.filter(checkDistrict);
        table.clear();
        table.rows.add(dataSet2).draw();
        //populate districts
        stationSet2 = stationSet.filter(checkStation);
        $('#station_id').empty();
        $('#station_id').append('<option value="-1"></option>');
        for(var counter=0;counter<stationSet2.length;counter++){
            $('#station_id').append('<option value="' + stationSet2[counter][0] + '">' + stationSet2[counter][0] + '(' + stationSet2[counter][1] + ')' + '</option>');
        }
    }else{
        table.clear();
        table.rows.add(dataSet).draw();
        $('#station_id').empty();
        $('#station_id').append('<option value="-1"></option>');
    }
    $('#station_id').val('-1');
}

function getIdFromEvent(e){
	try {
		newId = e.params.args.data.id;
		return newId;
	} catch(err){
		return -1;
	}
}

function addNewHospitalStationFromDistrict(e){
	newId = getIdFromEvent(e);
    if(newId != -1){
        //populate districts
        stationSet2 = stationSet.filter(checkStation);
        $('#newHospitalStation').empty();
        $('#newHospitalStation').append('<option value="-1"></option>');
        for(var counter=0;counter<stationSet2.length;counter++){
            $('#newHospitalStation').append('<option value="' + stationSet2[counter][0] + '">' + stationSet2[counter][0] + '(' + stationSet2[counter][1] + ')' + '</option>');
        }
    }else{
        $('#newHospitalStation').empty();
        $('#newHospitalStation').append('<option value="-1"></option>');
    }
    $('#station_id').val('-1');
}

function filterrrr2(){
    newDistrictId = $('#station_id').val();
    if(newDistrictId != -1){
        dataSet2 = dataSet.filter(abc);
        table.clear();
        table.rows.add(dataSet2).draw();
    }else{
        filterrrr();
    }
}

function abc(districts) {
    return districts['3'] == newDistrictId;
}

function checkDistrict(districts) {
    return districts['2'] == newId;
}

function checkStation(stations) {
    return stations['2'] == newId;
}

function locat(e) {
	var data = table.row( $(e).parents('tr') ).data();
    window.location.href = "hos_location?id=" + data[4];
}

function contact(e) {
	
	var data = table.row( $(e).parents('tr') ).data();
	
	var formData = {
			hospitalId : data[4],
			"_token": "{{ csrf_token() }}"
		} 	
	$('#manageHospitalId').val(data[4]);
	$('#modal_bd').empty();
	jQuery.ajax({
		url:'hospital_contacts',
		dataType:'json',
		type: 'post',
		data: formData,
		success:function(response){
			if(response.Status==0){
				alert(response.Message);
			} else {
				$('#staticModal').modal('show');
				var data = $.parseJSON(response.Data);
				for(var i=0; i< data.length; i++){
					addContactModal( data[i]['id'], data[i]['phone']);
				}
			}
		},error:function(){
			alert('Failed getting Contacts. Please consult IT support');
		}
	});
	
    
}

var dataSet = [
    @for($i=0;$i<count($hospitals);$i++)
    [
        "{{$hospitals[$i]['name']}}",
            "{{$hospitals[$i]['code']}}",
            "{{$hospitals[$i]['district']}}",
            "{{$hospitals[$i]['station']}}",
            "{{$hospitals[$i]['id']}}",
            "{{$hospitals[$i]['location']}}"
    ],
    @endfor
];

var stationSet = [
    @for($i=0;$i<count($stations);$i++)
    [
        "{{$stations[$i]['id']}}",
            "{{$stations[$i]['name']}}",
            "{{$stations[$i]['district']}}"
    ],
    @endfor
];
var stationSet2 = stationSet;

var dataSet2 = dataSet;
var table;
$(document).ready(function() {
	
$("#newHospitalDistrict").select2({width:"100%"}).on('select2:selecting',addNewHospitalStationFromDistrict);
$("#newHospitalStation").select2({width:"100%"});
			
table = $('#example').DataTable( {
    data: dataSet2,
    aaSorting: [],
    columns: [
        { title: "Name" },
        { title: "Code" },
        { title: "District" },
        { title: "Station" },
        { title: "" }
    ],

"columnDefs": [ {
    "targets": 4,
    "data": null,
    "sortable": false,
    "defaultContent": "<button type='button' class='btn btn-info btn-min-width location' onclick='locat(this)'>Location and More Info</button>"
	@if(Auth::check() && Auth::user()->memberOf('Secretariat Members')) + "<button type='button' class='btn btn-info btn-min-width contacts' onclick='contact(this)'>Manage Contacts</button>" @endif
} ]
} );



} );
</script>

@endsection
