<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="icon" href="media/maison.ico" type="image/x-icon">
	<title>Admin Dashboard</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<?php
		require "bootstrap.php";
	?>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
	
</head>
<body>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
		integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
		crossorigin=""/>
		<!-- Make sure you put this AFTER Leaflet's CSS -->
	<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
		integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
		crossorigin=""></script>

	<!--  nav bar -->
	<nav class="navbar sticky-top">
		<div class="icon-container">
			<img src="media/maison-logo.png" id="navBarLogo">
		</div>
		<div>
			<h5>Currently waiting : <span id="displayCurrentQueueLength"></span></h5>
		</div>
	</nav>

	<!-- floating window -->
	<div class="floating-window" id="myWindow">
		<div class="window-header" id="windowHeader">
			<span class="window-title " id="windowHeaderText">E.X.I.A.</span>
			<button class="window-close-btn" hidden>&times;</button>
			<div class="form-check form-switch h6 div-bottom-line">
				<input class="form-check-input" type="checkbox" role="switch" id="attendSwitch">
				<label class="form-check-label" for="attendSwitch" id="attendSwitchStatus">Idle</label>
			</div>
			<div class="form-check form-switch h6 div-bottom-line">
					<input class="form-check-input" type="checkbox" role="switch" id="deleteSwitch">
					<label class="form-check-label" for="deleteSwitch" id="deleteSwitchStatus">Delete Mode</label>
			</div>
			<button class="btn btn-outline-danger" onclick="logout()"><i class="bi bi-box-arrow-right"></i></button>
		</div>
		<div class="window-content">
			<div class="card reduced-padding" id="headingDequeue">
				<button  class="btn" data-toggle="collapse" data-target="#collapseDequeue" aria-expanded="false" aria-controls="collapseDequeue">
					<div class="card-header">
						<h6>Patient</h6>
					</div>
				</button>
				<div  id="collapseDequeue" class="collapse" aria-labelledby="headingDequeue" data-parent="#headingDequeue">
					<div class="card-body">
						<div class="row">
							<label for="displayQueueID">Queue Number</label>
							<input type="text" id="displayQueueID" disabled>
							<label for="displayQueueName">Name</label>
							<input type="text" id="displayQueueName" disabled>
							<label for="displayQueueGender">Gender</label>
							<input type="text" id="displayQueueGender" disabled>
							<label for="displayQueueAge">Age</label>
							<input type="text" id="displayQueueAge" disabled>
							<button class="btn btn-outline-warning" onclick="reinsertIntoSLQ()">
								Re-insert into SLQ
							</button>
							<button class="btn btn-outline-danger" onclick="concludePatient()">
								Conclude current patient
							</button>
							<button class="btn btn-outline-success" onclick="dequeuePatient()">
								Dequeue next patient
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="card reduced-padding" id="headingQueueInsertion">
				<button  class="btn" data-toggle="collapse" data-target="#collapseQueueInsertion" aria-expanded="false" aria-controls="collapseQueueInsertion">
					<div class="card-header">
						<h6>Queue Controls</h6>
					</div>
				</button>
				<div  id="collapseQueueInsertion" class="collapse" aria-labelledby="headingQueueInsertion" data-parent="#headingQueueInsertion">
					<div class="card-body">
						<div class="row">
							<button id="refreshProgressQueue" onclick="refreshProgressQueue()" class="btn btn-outline-primary">
							<i class="bi bi-arrow-clockwise h4"></i>
							</button>
							<button id="clearQueue" onclick="clearQueue()" class="btn btn-outline-danger delete-btn">
							<i class="bi bi-x-circle h4" ></i>
							</button>
							<div class="row row-top-margin div-left-line">
								<div>
									<p><b>General Patient Queue</b></p>
								</div>
								<div class="col-md">
									<input  class="col-md form-control" type="text" id="inputInsertGPQPatientICNum" placeholder="Patient IC">
								</div>
								<div class="col-sm-auto">
									<button  class="col- btn btn-outline-dark" id="dummyGPQ" onclick="dummyGPQ()"><i class="bi bi-chevron-double-right"></i></button>
								</div>
							</div>
							<div class="row row-top-margin div-left-line">
								<div>
									<p><b>Appointment Patient Queue</b></p>
								</div>
								<div class="col-md">
									<input  class="col-md form-control" type="text" id="inputDummyAPQ" placeholder="Appointment id">
								</div>
								<div class="col-sm-auto">
									<button onclick="processIntoAPQ()" class="btn btn-outline-dark"><i class="bi bi-chevron-double-right"></i></button>
								</div>
							</div>
								<div class="row row-top-margin div-left-line">
								<div>
									<p><b>Late Patient Queue</b></p>
								</div>
								<div class="col-md">
									<input  class="col-md form-control" class="col-md" type="text" id="inputDummyCBQ" placeholder="Appointment id">
								</div>
								<div class="col-sm-auto">
									<button onclick="processCBQ()" class="col- btn btn-outline-dark"><i class="bi bi-chevron-double-right"></i></button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- container -->
	<div class="container">
		
		<div class="row">
			<div class="card card-myconfig"  id="headingQueue">
				<button  class="btn" data-toggle="collapse" data-target="#collapseQueue" aria-expanded="false" aria-controls="collapseQueue">
					<div class="card-header">
						<h2>Queue</h2>
					</div>
				</button>
				<div  id="collapseQueue" class="collapse" aria-labelledby="headingQueue" data-parent="#headingQueue">
					<div class="col card-body" id="queueColumn">
						<div class="display-table-container">
							<h3>Second Level Queue</h3>
							<table id="displayPatientSLQ" class="display-table">
								<tr>
									<th>ID</th>
									<th>Before</th>
									<th>After</th>
									<th>Type</th>
									<th>IC</th>
									<th>Patient Details</th>
								</tr>
							</table>
						</div>
						<div class="display-table-container">
							<h3>Appointment Patient Queue</h3>
							<table id="displayPatientAPQ" class="display-table">
								<tr>
									<th>ID</th>
									<th>Before</th>
									<th>After</th>
									<th>Type</th>
									<th>IC</th>
									<th>Patient Details</th>
								</tr>
							</table>
						</div>
						<div class="display-table-container">
							<h3>General Patient Queue</h3>
							<table id="displayPatientGPQ" class="display-table">
								<tr>
									<th>ID</th>
									<th>Before</th>
									<th>After</th>
									<th>Type</th>
									<th>IC</th>
									<th>Patient Details</th>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="card card-myconfig" id="headingAppointment">
				<button  class="btn" data-toggle="collapse" data-target="#collapseAppointment" aria-expanded="false" aria-controls="collapseAppointment">
					<div class="card-header">
						<h2>Appointment</h2>
					</div>
				</button>
				<div  id="collapseAppointment" class="collapse" aria-labelledby="headingAppointment" data-parent="#headingAppointment">
					<div class="col card-body" id="appointmentColumn">
						<button id="refreshAppointment" onclick="fetchAppointment()" class="btn btn-outline-primary">
						<i class="bi bi-arrow-clockwise h4"></i>
						</button>
						<button class="btn btn-outline-success" id="btnInsertAppointment" onclick="showInsertionModal('appointment')" >
							<i class="bi bi-calendar-plus h4"></i>
						</button>
						<div class="table-container">
							<table id="displayAppointment" class="display-table">
								<tr>
									<th>ID</th>
									<th>Personnel ID</th>
									<th>Date Time</th>
									<th>Delete</th>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
			
		</div>
		<div class="row">
			<div class="card card-myconfig" id="headingPatient">
				<button  class="btn" data-toggle="collapse" data-target="#collapsePatient" aria-expanded="false" aria-controls="collapsePatient">
					<div class="card-header">
						<h2>Patient</h2>
					</div>
				</button>
				<div  id="collapsePatient" class="collapse" aria-labelledby="headingPatient" data-parent="#headingPatient">
					<div class="card-body" id="patientColumn">
						<button id="refreshPatient" onclick="fetchPatient()" class="btn btn-outline-primary">
							<i class="bi bi-arrow-clockwise h4"></i>
						</button>
						<button class="btn btn-outline-success" id="btnInsertPatient" onclick="showInsertionModal('patient')">
							<i class="bi bi-person-plus h4"></i>
						</button>
							<input type="text" id="searchBarPatient" class="input">
							<label for="searchBarPatient"><i class="bi bi-search"></i></label>
						<div class="table-container">
							<table id="displayPatient" class="display-table">
								<tr>
									<th>No.</th>
									<th>Name</th>
									<th>IC</th>
									<th>Gender</th>
									<th>Age</th>
									<th>Email</th>
									<th>Phone</th>
									<th>Actions</th>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
			
		</div>
		<div class="row">
			<div class="card card-myconfig" id="headingPersonnel">
				<button  class="btn" data-toggle="collapse" data-target="#collapsePersonnel" aria-expanded="false" aria-controls="collapsePersonnel">
					<div class="card-header">
						<h2>Personnel</h2>
					</div>
				</button>
				<div  id="collapsePersonnel" class="collapse" aria-labelledby="headingPersonnel" data-parent="#headingPersonnel">
					<div class="col card-body" id="personnelColumn">
						<button id="refreshPersonnel" onclick="fetchPersonnel()" class="btn btn-outline-primary">
							<i class="bi bi-arrow-clockwise h4"></i>
						</button>
						<button class="btn btn-outline-success" id="btnInsertPersonnel" onclick="showInsertionModal('personnel')">
							<i class="bi bi-person-plus h4"></i>
						</button>
						<input type="text" id="searchBarPersonnel" class="input">
						<label for="searchBarPersonnel"><i class="bi bi-search"></i></label>
						<div class="table-container">
							<table id="displayPersonnel" class="display-table">
								<tr>
									<th>No.</th>
									<th>ID</th>
									<th>Name</th>
									<th>IC</th>
									<th>Gender</th>
									<th>Age</th>
									<th>Email</th>
									<th>Phone</th>
									<th>Type</th>
									<th>Department</th>
									<th>Present</th>
									<th>Actions</th>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
			
		</div>
		<div class="row">
			<div class="card card-myconfig" id="headingDepartment">
				<button  class="btn" data-toggle="collapse" data-target="#collapseDepartment" aria-expanded="false" aria-controls="collapseDepartment">
					<div class="card-header">
						<h2>Department</h2>
					</div>
				</button>
				<div  id="collapseDepartment" class="collapse" aria-labelledby="headingDepartment" data-parent="#headingDepartment">
					<div class="col card-body" id="departmentColumn">
						<button id="refreshDepartment" onclick="fetchDepartment()" class="btn btn-outline-primary">
							<i class="bi bi-arrow-clockwise h4"></i>
						</button>
						<button class="btn btn-outline-success" id="btnInsertDepartment" onclick="showInsertionModal('department')">
							<i class="bi bi-journal-plus h4"></i>
						</button>
						<input type="text" id="searchBarDepartment" class="input">
						<label for="searchBarDepartment"><i class="bi bi-search"></i></label>
						<div class="table-container">
							<table id="displayDepartment" class="display-table">
								<tr>
									<th>Code</th>
									<th>Name</th>
									<th>Desc</th>
									<th>Count</th>
									<th>Actions</th>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
			
		</div>
		<div class="row">
			<div class="card card-myconfig" id="headingService">
				<button  class="btn" data-toggle="collapse" data-target="#collapseService" aria-expanded="false" aria-controls="collapseService">
					<div class="card-header">
						<h2>Service</h2>
					</div>
				</button>
				<div  id="collapseService" class="collapse" aria-labelledby="headingService" data-parent="#headingService">
					<div class="col card-body" id="serviceColumn">
						<button id="refreshService" onclick="fetchService()" class="btn btn-outline-primary">
							<i class="bi bi-arrow-clockwise h4"></i>
						</button>
						<button class="btn btn-outline-success" id="btnInsertService" onclick="showInsertionModal('service')">
							<i class="bi bi-clipboard-plus h4"></i>
						</button>
						<input type="text" id="searchBarService" class="input">
						<label for="searchBarService"><i class="bi bi-search"></i></label>
						<div class="table-container">
							<table id="displayService" class="display-table">
								<tr>
									<th>Code</th>
									<th>Desc</th>
									<th>Available</th>
									<th>Dept</th>
									<th>Actions</th>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
			
		</div>
		<div class="row">
			<div class="card card-myconfig" id="headingCBQ">
				<button  class="btn" data-toggle="collapse" data-target="#collapseCBQ" aria-expanded="false" aria-controls="collapseCBQ" id="toggleCBQSectionButton">
					<div class="card-header">
						<h2>CBQ Config</h2>
					</div>
				</button>
				<div  id="collapseCBQ" class="collapse" aria-labelledby="headingCBQ" data-parent="#headingCBQ">
					<div class="col card-body" id="cbqConfigColumn">
							<div id="map"></div>
							<p>Crosshair by: <a href="https://thenounproject.com/rohithdezinr/" target="_blank">Rohith M S</a>.</p>
						<div class="card" id="headingMap">
							<button  class="btn" data-toggle="collapse" data-target="#collapseMap" aria-expanded="false" aria-controls="collapseMap">
								<div class="card-header">
									<h6>Clinic location config</h6>
								</div>
							</button>
							<div  id="collapseMap" class="collapse" aria-labelledby="headingMap" data-parent="#headingMap">
								<div class="card-body">
									<div class="row">
										<label class="form-label" for="customRange1">Queue Radius</label>
										<div class="range">
											<input class="form-range" type="range" min="50" max="700" value="50" class="slider" id="myRange" type="range" class="form-range" id="customRange1" />
											<p>Radius:
												<span id="demo"></span>
										</div>
										<button class="button" onclick="setClinicLocation()"><i class="bi bi-pin-map"></i>	Set Location & Radius	<i class="bi bi-pin-map"></i></button>
									</div>
								</div>
							</div>
						</div>
						<div class="card" id="headingCap">
							<button  class="btn" data-toggle="collapse" data-target="#collapseCap" aria-expanded="false" aria-controls="collapseCap">
								<div class="card-header">
									<h6>Set clinic capacity</h6>
								</div>
							</button>
							<div  id="collapseCap" class="collapse" aria-labelledby="headingCap" data-parent="#headingCap">
								<div class="card-body">
									<div class="row">
										<input type="text" id="inputClinicCapacity" placeholder="Enter capacity" style="text-align:center;">
										<button onclick="setClinicCapacity()" class="button" style="margin-top: 10px;">Set Capacity</button>
									</div>
								</div>
							</div>
						</div>
						<button id="fetchCBQConfiq" onclick="fetchCBQConfig()" class="btn btn-outline-primary"><i class="bi bi-arrow-clockwise h4"></i></button>
						<p>Currently patient per present doctor: <span id="outputPatientPerDr"></span></p>
						<p>Queue length: <span id="outputQueueLength"></span></p>
						<p>Doctors present: <span id="outputDrCount"></span></p>
						<div class="table-container">
							<table id="displayCBQConfig" class="display-table">
								<tr>
									<th>Preset</th>
									<th>X</th>
									<th>Y</th>
									<th>min_support</th>
									<th>max_support</th>
									<th>active</th>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
			
		</div>
		
		</div>
		<!-- Content cover div -->
		<div class="content-cover" id="block-login">
  <div class="row justify-content-center align-items-center">
    <div class="col-lg-4 col-md-6">
      <div class="login-form text-center">
        <div class="image-container">
          <img src="media/motion logo.gif" class="resized-image">
        </div>
		<p id="loginFeedback" class="red-bold-text"></p>
        <h2>Login</h2>
        <div class="form-group">
          <label for="login_id">Personnel ID:</label>
          <input type="text" id="login_id" class="form-control">
        </div>
        <div class="form-group">
          <label for="login_icnum">Personnel IC Number:</label>
          <input type="text" id="login_icnum" class="form-control">
        </div>
        <button class="button" onclick="login()">Submit</button>
      </div>
    </div>
  </div>
</div>


	<!-- INSERTION MODAL -->
	<div id="insertionModal" class="modal">
		<!-- Modal content -->
		<div class="modal-content">
		<div class="modal-header">
			<span class="close">&times;</span>
			<h2>Insertion</h2>
		</div>
		<div class="modal-body">
			<p id="feedbackSpace"></p>
			<div class="modal-form" id="displayPatientForm">
				<h4>Patient</h4>
				<table>
					<tr>
						<td><label for="inputPatientICNum">IC Number</label></td>
						<td><input type="text" id="inputPatientICNum"></td>
					</tr>
					<tr>
						<td><label for="inputPatientName">Name</label></td>
						<td><input type="text" id="inputPatientName"></td>
					</tr>
					<tr>
						<td><label for="inputPatientPhoneNumber">Phone Number</label></td>
						<td><input type="text" id="inputPatientPhoneNumber"></td>
					</tr>
					<tr>
						<td><label for="inputPatientEmail">Email</label></td>
						<td><input type="email" id="inputPatientEmail"></td>
					</tr>
				</table>
				
			</div>
			<div class="modal-form" id="displayAppointmentForm">
				<h4>Appointment</h4>
				<table>
					<tr>
						<td><label for="inputPatientICNumApp">Patient IC</label></td>
						<td><input type="text" id="inputPatientICNumApp" class="form-control"></td>
						<td><button class="button" onclick="autofetchCurrentDequeuedPatient()">Fetch Current Patient</button></td>
					</tr>
					<tr>
						<td><label for="serviceDropdownApp">Service Type</label></td>
						<td>
							<select id="serviceDropdownApp" class="form-control">
							</select>
						</td>
						<td><button class="button" onclick="fetchService()">Refresh List</button></td>
					</tr>
					<tr>
						<td><label for="inputDateApp">Date</label></td>
						<td><input type="date" id="inputDateApp" name="date" class="form-control"></td>
					</tr>
					<tr>
						<td><label for="inputTimeApp">Time</label></td>
						<td><input type="time" id="inputTimeApp" min="08:00" max="16:30" class="form-control"></td>
					</tr>
				</table>
			</div>
			<div class="modal-form" id="displayPersonnelForm">
				<h4>Personnel</h4>
				<table>
					<tr>
						<td><label for="inputPersonnelName">Name</label></td>
						<td><input type="text" id="inputPersonnelName"></td>
					</tr>
					<tr>
						<td><label for="inputPersonnelICNumber">IC Number</label></td>
						<td><input type="text" id="inputPersonnelICNumber"></td>
					</tr>
					<tr>
						<td><label for="inputPersonnelPhoneNumber">Phone Number</label></td>
						<td><input type="text" id="inputPersonnelPhoneNumber"></td>
					</tr>
					<tr>
						<td><label for="inputPersonnelEmail">Email</label></td>
						<td><input type="email" id="inputPersonnelEmail"></td>
					</tr>
					<tr>
						<td><label for="inputPersonnelDepartment">Department</label></td>
						<td>
							<select id="departmentDropdownPersonnel">
							</select>
						</td>
						<td><button class="button" onclick="fetchDepartment()">Refresh List</button></td>
					</tr>
					<tr>
						<td><label for="inputPersonnelType">Type/Position</label></td>
						<td><input type="text" id="inputPersonnelType"></td>
					</tr>
				</table>
			</div>
			<div class="modal-form" id="displayDepartmentForm">
				<h4>Department</h4>
				<table>
					<tr>
						<td><label for="inputDepartmentCode">Code</label></td>
						<td><input type="text" id="inputDepartmentCode"></td>
					</tr>
					<tr>
						<td><label for="inputDepartmentName">Name</label></td>
						<td><input type="text" id="inputDepartmentName"></td>
					</tr>
					<tr>
						<td><label for="inputDepartmentDesc">Description</label></td>
						<td><input type="text" id="inputDepartmentDesc"></td>
					</tr>
				</table>
			</div>
			<div class="modal-form" id="displayServiceForm">
				<h4>Service</h4>
				<table>
					<tr>
						<td><label for="inputServiceCode">Code</label></td>
						<td><input type="text" id="inputServiceCode"></td>
					</tr>
					<tr>
						<td><label for="inputServiceDesc">Description</label></td>
						<td><input type="text" id="inputServiceDesc"></td>
					</tr>
					<tr>
						<td><label for="inputServiceDepartment">Department</label></td>
						<td>
							<select id="departmentDropdownService">
							</select>
						</td>
						<td><button class="button" onclick="fetchDepartment()">Refresh List</button></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="modal-footer">
			<button class="button" id="generalInsertButton" onclick="generalInsertion()">Insert</button>
		</div>
	</div>

	
</div>
<!-- MODIFICATION MODAL -->
<div id="modificationModal" class="modal">
		<!-- Modal content -->
		<div class="modal-content">
		<div class="modal-header" style="background-color: #fff700;">
			<span class="close">&times;</span>
			<h2>Modification</h2>
		</div>
		<div class="modal-body">
			<p id="feedbackSpaceUpdate"></p>
			<div class="modal-form" id="displayPatientUpdateForm">
				<h4>Patient</h4>
				<table>
					<tr>
					<td><label for="inputUpdatePatientICNum">IC Number</label></td>
					<td><input type="text" id="inputUpdatePatientICNum"></td>
					</tr>
					<tr>
					<td><label for="inputUpdatePatientName">Name</label></td>
					<td><input type="text" id="inputUpdatePatientName"></td>
					</tr>
					<tr>
					<td><label for="inputUpdatePatientPhoneNumber">Phone Number</label></td>
					<td><input type="text" id="inputUpdatePatientPhoneNumber"></td>
					</tr>
					<tr>
					<td><label for="inputUpdatePatientEmail">Email</label></td>
					<td><input type="email" id="inputUpdatePatientEmail"></td>
					</tr>
				</table>
			</div>
			<div class="modal-form" id="displayAppointmentUpdateForm">
				<h4>Appointment</h4>
				<table>
					<tr>
					<td><label for="inputUpdatePatientICNumApp">Patient IC</label></td>
					<td><input type="text" id="inputUpdatePatientICNumApp"></td>
					<td><button class="button">Fetch Current Patient</button></td>
					</tr>
					<tr>
					<td><label for="serviceDropdownUpdateApp">Service Type</label></td>
					<td>
						<select id="serviceDropdownUpdateApp">
						</select>
					</td>
					<td><button class="button" onclick="fetchService()">Refresh List</button></td>
					</tr>
					<tr>
					<td><label for="inputUpdateDateApp">Date</label></td>
					<td><input type="date" id="inputUpdateDateApp" name="date"></td>
					</tr>
					<tr>
					<td><label for="inputUpdateTimeApp">Time</label></td>
					<td><input type="time" id="inputUpdateTimeApp"></td>
					</tr>
				</table>
			</div>
			<div class="modal-form" id="displayPersonnelUpdateForm">
				<h4>Personnel</h4>
				<table>
					<tr>
					<td><label for="inputUpdatePersonnelName">Name</label></td>
					<td><input type="text" id="inputUpdatePersonnelName"></td>
					</tr>
					<tr>
					<td><label for="inputUpdatePersonnelICNumber">IC Number</label></td>
					<td><input type="text" id="inputUpdatePersonnelICNumber"></td>
					</tr>
					<tr>
					<td><label for="inputUpdatePersonnelPhoneNumber">Phone Number</label></td>
					<td><input type="text" id="inputUpdatePersonnelPhoneNumber"></td>
					</tr>
					<tr>
					<td><label for="inputUpdatePersonnelEmail">Email</label></td>
					<td><input type="email" id="inputUpdatePersonnelEmail"></td>
					</tr>
					<tr>
					<td><label for="inputUpdatePersonnelDepartment">Department</label></td>
					<td>
						<select id="departmentDropdownUpdatePersonnel">
						</select>
					</td>
					<td><button class="button" onclick="fetchDepartment()">Refresh List</button></td>
					</tr>
					<tr>
					<td><label for="inputUpdatePersonnelType">Type/Position</label></td>
					<td><input type="text" id="inputUpdatePersonnelType"></td>
					</tr>
				</table>
			</div>
			<div class="modal-form" id="displayDepartmentUpdateForm">
				<h4>Department</h4>
				<table>
					<tr>
					<td><label for="inputUpdateDepartmentCode">Code</label></td>
					<td><input type="text" id="inputUpdateDepartmentCode"></td>
					</tr>
					<tr>
					<td><label for="inputUpdateDepartmentName">Name</label></td>
					<td><input type="text" id="inputUpdateDepartmentName"></td>
					</tr>
					<tr>
					<td><label for="inputUpdateDepartmentDesc">Description</label></td>
					<td><input type="text" id="inputUpdateDepartmentDesc"></td>
					</tr>
				</table>
			</div>
			<div class="modal-form" id="displayServiceUpdateForm">
				<h4>Service</h4>
				<table>
					<tr>
					<td><label for="inputUpdateServiceCode">Code</label></td>
					<td><input type="text" id="inputUpdateServiceCode"></td>
					</tr>
					<tr>
					<td><label for="inputUpdateServiceDesc">Description</label></td>
					<td><input type="text" id="inputUpdateServiceDesc"></td>
					</tr>
					<tr>
					<td><label for="inputUpdateServiceDepartment">Department</label></td>
					<td>
						<select id="departmentDropdownUpdateService">
						</select>
					</td>
					<td><button class="button" onclick="fetchDepartment()">Refresh List</button></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="modal-footer">
			<input type="hidden" id="updateFormSaveID"></input>
			<button class="button" id="generalModifyButton" onclick="generalModification()">Modify</button>
		</div>
	</div>
	
	

</div>

	<!-- PATIENT DETAILS MODAL -->
<div id="patientDetailModal" class="modal">
		<!-- Modal content -->
		<div class="modal-content">
		<div class="modal-header" style="background-color: #99c5ff;">
			<span class="close">&times;</span>
			<h2>Details</h2>
		</div>
		<div class="modal-body">
			<div class="modal-form" id="displayOnlyPatient">
				<h4>Patient</h4>
				<table>
					<tr>
					<td><label for="displayPatientName">Name</label></td>
					<td><input type="text" id="displayPatientName" disabled></td>
					</tr>
					<tr>
					<td><label for="displayPatientAge">Age</label></td>
					<td><input type="text" id="displayPatientAge" disabled></td>
					</tr>
					<tr>
					<td><label for="displayPatientGender">Gender</label></td>
					<td><input type="text" id="displayPatientGender" disabled></td>
					</tr>
					<tr>
					<td><label for="displayPatientPhoneNumber">Phone Number</label></td>
					<td><input type="text" id="displayPatientPhoneNumber" disabled></td>
					</tr>
					<tr>
					<td><label for="displayPatientEmail">Email</label></td>
					<td><input type="email" id="displayPatientEmail" disabled></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="modal-footer">
		</div>
	</div>
</div>
	<script src="map scripts.js"></script>
	<script src="script.js"></script>
	
</body>
</html>
