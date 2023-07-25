
var personnelDataArray = [];

function fetchPersonnel() {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                //response
                //document.getElementById("testOutput").innerHTML = this.responseText;
                //alert("function triggered");
                    var response = xmlhttp.responseText;
                    var personnelData = JSON.parse(response);
                    personnelDataArray = [];
                    personnelDataArray = personnelData;
                    //var personnelData = this.responseText;
                    // Get the table element by its ID
                    var table = document.getElementById('displayPersonnel');

                        // Clear existing table rows
                    while (table.rows.length > 1) {
                        table.deleteRow(1);
                    }

                    // Loop through the personnel data array
                    personnelData.forEach(function(person, index) {
                    // Create a new row for each personnel
                    var row = table.insertRow();
            
                    // Create cells for each column
                    var cellNo = row.insertCell();
                    var cellID = row.insertCell();
                    var cellName = row.insertCell();
                    var cellIC = row.insertCell();
                    var cellGender = row.insertCell();
                    var cellAge = row.insertCell();
                    var cellEmail = row.insertCell();
                    var cellPhone = row.insertCell();
                    var cellType = row.insertCell();
                    var cellDept = row.insertCell();
                    var cellPresent = row.insertCell();
            
                    // Populate the cell values with personnel data
                    cellNo.innerHTML = index + 1; // Number the rows sequentially
                    cellID.innerHTML = person.personnel_ID;
                    cellName.innerHTML = person.personnel_name;
                    cellIC.innerHTML = person.personnel_ICNum;
                    cellGender.innerHTML = person.personnel_gender;
                    cellAge.innerHTML = person.personnel_age;
                    cellEmail.innerHTML = person.personnel_email;
                    cellPhone.innerHTML = person.personnel_phoneNum;
                    cellType.innerHTML = person.personnel_type;
                    cellDept.innerHTML = person.dept_code;
                    cellPresent.innerHTML = person.personnel_attend; // Set the Present column empty for now
                    
                    //create cell for action button
                    var cellAction = row.insertCell();
                    // Create the button element for delete action
                    var btnDelete = document.createElement("button");
                    btnDelete.setAttribute("type", "button");
                    btnDelete.setAttribute("class", "btn btn-outline-danger delete-btn");
                    btnDelete.setAttribute("disabled", "");
                    
                    var deleteIcon = document.createElement("i");
                    deleteIcon.classList.add("bi");
                    deleteIcon.classList.add("bi-trash3");
                    btnDelete.appendChild(deleteIcon);
                    
                    btnDelete.onclick = function() {
                      var targetField = "personnel"; // Replace with the appropriate target field value
                      var primaryKey = person.personnel_ID; // Replace with the actual primary key value
                      generalDeletion(targetField, primaryKey);
                    };
                    cellAction.appendChild(btnDelete);

                    // Create the edit button
                    var editButton = document.createElement("button");
                    editButton.setAttribute("type", "button");
                    editButton.setAttribute("class", "btn btn-outline-warning edit-btn");
                    
                    var editIcon = document.createElement("i");
                    editIcon.classList.add("bi");
                    editIcon.classList.add("bi-pencil-square");
                    editButton.appendChild(editIcon);

                    // Add onclick event to the edit button
                    editButton.onclick = function() {
                      var type = "personnel"; // Replace with the appropriate type
                      var id = person.personnel_ID; // Replace with the actual ID
                      showModificationModal(type, id);
                    };

                    cellAction.appendChild(editButton);

                  
                  });
        }
        };
        var method = "method="+"fetchAllPersonnel";
        xmlhttp.open("GET", "manage personnel.php?" + method, true);
        xmlhttp.send();
}

var patientDataArray = [];
function fetchPatient() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var response = xmlhttp.responseText;
            var patientData = JSON.parse(response);

            patientDataArray = [];
            patientDataArray = patientData;
            //alert("patient data flushed: "+ JSON.stringify(patientDataArray));
            var table = document.getElementById('displayPatient');
            //document.getElementById("testOutput").innerHTML = this.responseText;
            // Clear existing table rows
            while (table.rows.length > 1) {
                table.deleteRow(1);
            }

            patientData.forEach(function(patient, index) {
                var row = table.insertRow();
                var cellNo = row.insertCell();
                var cellName = row.insertCell();
                var cellIC = row.insertCell();
                var cellGender = row.insertCell();
                var cellAge = row.insertCell();
                var cellEmail = row.insertCell();
                var cellPhone = row.insertCell();

                cellNo.innerHTML = index + 1;
                cellName.innerHTML = patient.patient_name;
                cellIC.innerHTML = patient.patient_ICNum;
                cellGender.innerHTML = patient.patient_gender;
                cellAge.innerHTML = patient.patient_age;
                cellEmail.innerHTML = patient.patient_email;
                cellPhone.innerHTML = patient.patient_phoneNum;

                //create cell for action button
                var cellAction = row.insertCell();
                // Create the button element for delete action
                var btnDelete = document.createElement("button");
                btnDelete.setAttribute("type", "button");
                btnDelete.setAttribute("class", "btn btn-outline-danger delete-btn");
                btnDelete.setAttribute("disabled", "");

                var deleteIcon = document.createElement("i");
                deleteIcon.classList.add("bi");
                deleteIcon.classList.add("bi-trash3");
                btnDelete.appendChild(deleteIcon);

                btnDelete.onclick = function() {
                  var targetField = "patient"; // Replace with the appropriate target field value
                  var primaryKey = patient.patient_ICNum; // Replace with the actual primary key value
                  generalDeletion(targetField, primaryKey);
                };
                cellAction.appendChild(btnDelete);

                // Create the edit button
                var editButton = document.createElement("button");
                editButton.setAttribute("type", "button");
                editButton.setAttribute("class", "btn btn-outline-warning edit-btn");

                var editIcon = document.createElement("i");
                editIcon.classList.add("bi");
                editIcon.classList.add("bi-pencil-square");
                editButton.appendChild(editIcon);

                // Add onclick event to the edit button
                editButton.onclick = function() {
                  var type = "patient"; // Replace with the appropriate type
                  var id = patient.patient_ICNum; // Replace with the actual ID
                  showModificationModal(type, id);
                };

                cellAction.appendChild(editButton);
            });
        }
    };

    var method = "method=" + "fetchAllPatient";
    xmlhttp.open("GET", "manage patient.php?" + method, true);
    xmlhttp.send();
}

var queueDataArray = [];
function fetchQueue() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var response = xmlhttp.responseText;
            //alert("raw data queues: "+response);
            try{
              var queueData = response.split("##@@!!");
              queueDataArray = queueData;
              //var queueData = JSON.parse(JSON.parse(response));
              //alert("queue data flushed: "+ JSON.stringify(queueData));
              //alert("fetched queues: "+queueDataArray);
              // Clear existing table rows for SLQ
              clearTableRows("displayPatientSLQ");
              // Insert SLQ data if not empty
              //alert("0th index: "+queueData[0]);
              if (queueData[0] !== "0") {
                  var slqData = JSON.parse(queueData[0]);
                  insertQueueData(slqData, "displayPatientSLQ");
              }
              //alert("1st index: "+queueData[1]);
              // Clear existing table rows for APQ
              clearTableRows("displayPatientAPQ");
              // Insert APQ data if not empty
              if (queueData[1] !== "0") {
                  var apqData = JSON.parse(queueData[1]);
                  insertQueueData(apqData, "displayPatientAPQ");
              }
              //alert("2nd index: "+queueData[2]);
              // Clear existing table rows for GPQ
              clearTableRows("displayPatientGPQ");
              // Insert GPQ data if not empty
              if (queueData[2] !== "0") {
                  var gpqData = JSON.parse(queueData[2]);
                  insertQueueData(gpqData, "displayPatientGPQ");
              }
            }catch (error){
              //alert(this.responseText);
              alert("error: "+error);
            }
            
        }
    };

    var method = "method=" + "fetchAllQueue";
    xmlhttp.open("GET", "manage queue.php?" + method, true);
    xmlhttp.send();
}

var departmentDataArray = [];
function fetchDepartment() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var response = xmlhttp.responseText;
        var departmentData = JSON.parse(response);
        departmentDataArray = [];
        departmentDataArray = departmentData;

        var table = document.getElementById('displayDepartment');
        var dropdownPersonnel = document.getElementById('departmentDropdownPersonnel');
        var dropdownService = document.getElementById('departmentDropdownService');
        var dropdownUpdatePersonnel = document.getElementById('departmentDropdownUpdatePersonnel');
        var dropdownUpdateService = document.getElementById('departmentDropdownUpdateService');

        // Clear existing table rows
        while (table.rows.length > 1) {
          table.deleteRow(1);
        }
  
        // Clear existing dropdown options
        dropdownPersonnel.innerHTML = '';
        dropdownService.innerHTML = '';
        dropdownUpdatePersonnel.innerHTML = '';
        dropdownUpdateService.innerHTML = '';
  
        // Populate the table and dropdowns with department data
        if (departmentData != null && departmentData.length > 0) {
          departmentData.forEach(function(department, index) {
            var row = table.insertRow(index + 1);
            var codeCell = row.insertCell(0);
            var nameCell = row.insertCell(1);
            var descCell = row.insertCell(2);
            var headCountCell = row.insertCell(3);
  
            codeCell.innerHTML = department.dept_code;
            nameCell.innerHTML = department.dept_name;
            descCell.innerHTML = department.dept_desc;
            headCountCell.innerHTML = department.dept_headCount;
            
            // Populate the personnel dropdown with department options
            var optionPersonnel = document.createElement('option');
            optionPersonnel.value = department.dept_code;
            optionPersonnel.textContent = department.dept_name;
            dropdownPersonnel.appendChild(optionPersonnel);
  
            // Populate the service dropdown with department options
            var optionService = document.createElement('option');
            optionService.value = department.dept_code;
            optionService.textContent = department.dept_name;
            dropdownService.appendChild(optionService);
            
            var optionService = document.createElement('option');
            optionService.value = department.dept_code;
            optionService.textContent = department.dept_name;
            dropdownUpdatePersonnel.appendChild(optionService);

            var optionService = document.createElement('option');
            optionService.value = department.dept_code;
            optionService.textContent = department.dept_name;
            dropdownUpdateService.appendChild(optionService);
            

            //create cell for action button
            var cellAction = row.insertCell();
            // Create the button element for delete action
            var btnDelete = document.createElement("button");
            btnDelete.setAttribute("type", "button");
            btnDelete.setAttribute("class", "btn btn-outline-danger delete-btn");
            btnDelete.setAttribute("disabled", "");
            
            var deleteIcon = document.createElement("i");
            deleteIcon.classList.add("bi");
            deleteIcon.classList.add("bi-trash3");
            btnDelete.appendChild(deleteIcon);

            btnDelete.onclick = function() {
              var targetField = "department"; // Replace with the appropriate target field value
              var primaryKey = department.dept_code; // Replace with the actual primary key value
              generalDeletion(targetField, primaryKey);
            };
            cellAction.appendChild(btnDelete);

            // Create the edit button
            var editButton = document.createElement("button");
            editButton.setAttribute("type", "button");
            editButton.setAttribute("class", "btn btn-outline-warning edit-btn");
            
            var editIcon = document.createElement("i");
            editIcon.classList.add("bi");
            editIcon.classList.add("bi-pencil-square");
            editButton.appendChild(editIcon);

            // Add onclick event to the edit button
            editButton.onclick = function() {
              var type = "department"; // Replace with the appropriate type
              var id = department.dept_code; // Replace with the actual ID
              showModificationModal(type, id);
            };

            cellAction.appendChild(editButton);
          });
        } else {
          // Display a message when there is no department data
          var emptyRow = table.insertRow(1);
          var emptyCell = emptyRow.insertCell(0);
          emptyCell.colSpan = "4";
          emptyCell.textContent = "No departments found.";
  
          // Add a default option to the personnel dropdown
          var optionPersonnel = document.createElement('option');
          optionPersonnel.textContent = 'No departments found.';
          dropdownPersonnel.appendChild(optionPersonnel);
  
          // Add a default option to the service dropdown
          var optionService = document.createElement('option');
          optionService.textContent = 'No departments found.';
          dropdownService.appendChild(optionService);
        }
      }
    };
  
    var method = "method=" + "fetchAllDepartment";
    xmlhttp.open("GET", "manage department.php?" + method, true);
    xmlhttp.send();
}
  
var serviceDataArray = [];
function fetchService() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
        var response = xmlhttp.responseText;
        var serviceData = JSON.parse(response);
        serviceDataArray = [];
        serviceDataArray = serviceData;
        var table = document.getElementById('displayService');
        var dropdown = document.getElementById('serviceDropdownApp');

        // Clear existing table rows
        while (table.rows.length > 1) {
            table.deleteRow(1);
        }

        // Clear existing dropdown options
        dropdown.innerHTML = '';

        // Populate the table with service data
        if (serviceData != null && serviceData.length > 0) {
          serviceData.forEach(function(service, index) {
            var row = table.insertRow(index + 1);
            var codeCell = row.insertCell(0);
            var descCell = row.insertCell(1);
            var feeCell = row.insertCell(2);
            var deptCell = row.insertCell(3);
  
            codeCell.innerHTML = service.svc_code;
            descCell.innerHTML = service.svc_desc;
            feeCell.innerHTML = service.svc_enable;
            deptCell.innerHTML = service.dept_code;
  
            // Populate the dropdown with service options
            var option = document.createElement('option');
            option.value = service.svc_code;
            option.innerHTML = service.svc_desc;
            dropdown.appendChild(option);

            //create cell for action button
            var cellAction = row.insertCell();
            // Create the button element for delete action
            var btnDelete = document.createElement("button");
            btnDelete.setAttribute("type", "button");
            btnDelete.setAttribute("class", "btn btn-outline-danger delete-btn");
            btnDelete.setAttribute("disabled", "");

            var deleteIcon = document.createElement("i");
            deleteIcon.classList.add("bi");
            deleteIcon.classList.add("bi-trash3");
            btnDelete.appendChild(deleteIcon);

            btnDelete.onclick = function() {
              var targetField = "service"; // Replace with the appropriate target field value
              var primaryKey = service.svc_code; // Replace with the actual primary key value
              generalDeletion(targetField, primaryKey);
            };
            cellAction.appendChild(btnDelete);

            // Create the edit button
            var editButton = document.createElement("button");
            editButton.setAttribute("type", "button");
            editButton.setAttribute("class", "btn btn-outline-warning edit-btn");

            var editIcon = document.createElement("i");
            editIcon.classList.add("bi");
            editIcon.classList.add("bi-pencil-square");
            editButton.appendChild(editIcon);

            // Add onclick event to the edit button
            editButton.onclick = function() {
              var type = "service"; // Replace with the appropriate type
              var id = service.svc_code; // Replace with the actual ID
              showModificationModal(type, id);
            };

            cellAction.appendChild(editButton);
          });
        } else {
          // Display a message when there is no service data
          var emptyRow = table.insertRow(1);
          var emptyCell = emptyRow.insertCell(0);
          emptyCell.colSpan = "4";
          emptyCell.textContent = "No services found.";
  
          // Add a default option to the dropdown
          var option = document.createElement('option');
          option.textContent = 'No services found.';
          dropdown.appendChild(option);
        }
      }
    };
  
    var method = "method=" + "fetchAllService";
    xmlhttp.open("GET", "manage service.php?" + method, true);
    xmlhttp.send();
}

var appointmentDataArray = [];
function fetchAppointment() {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      var response = xmlhttp.responseText;
  
      try {
        var appointmentData;
        if (response !== null && response !== "") {
          appointmentData = JSON.parse(response);
          appointmentDataArray = [];
          appointmentDataArray = appointmentData;
        } else {
          appointmentData = null;
        }
      } catch {
        alert(this.responseText);
      }
  
      var table = document.getElementById('displayAppointment');
  
      // Clear existing table rows
      while (table.rows.length > 1) {
        table.deleteRow(1);
      }
  
      // Populate the table with appointment data
      if (appointmentData != null && appointmentData.length > 0) {
        appointmentData.forEach(function(appointment, index) {
          var row = table.insertRow(index + 1);
          var idCell = row.insertCell(0);
          var personnelIdCell = row.insertCell(1);
          var dateTimeCell = row.insertCell(2);
  
          idCell.innerHTML = appointment.q_ID;
          personnelIdCell.innerHTML = appointment.personnel_ID;
          dateTimeCell.innerHTML = appointment.app_datetime;
  
          // Set the desired timezone
          const timeZone = 'Asia/Kuala_Lumpur';

          // Create a new Date object adjusted for the specified timezone
          const today = new Date().toLocaleString('en-US', { timeZone });

          // Get the current date in the specified timezone
          const todayDate = today.slice(0, 10); // Format: YYYY-MM-DD

          // Check if the date is today and the personnel ID matches
          if (
            appointment.app_datetime.slice(0, 10) === todayDate &&
            appointment.personnel_ID === globalPersonnelID
          ) {
            row.classList.add('todayAppointment'); // Add the CSS class to the row's class list
          }

          //create cell for action button
          var cellAction = row.insertCell();
          // Create the button element for delete action
          var btnDelete = document.createElement("button");
          btnDelete.setAttribute("type", "button");
          btnDelete.setAttribute("class", "btn btn-outline-danger delete-btn");
          btnDelete.setAttribute("disabled", "");

          var deleteIcon = document.createElement("i");
          deleteIcon.classList.add("bi");
          deleteIcon.classList.add("bi-trash3");
          btnDelete.appendChild(deleteIcon);

          btnDelete.onclick = function() {
            var targetField = "appointment"; // Replace with the appropriate target field value
            var primaryKey = appointment.q_ID; // Replace with the actual primary key value
            generalDeletion(targetField, primaryKey);
          };
          cellAction.appendChild(btnDelete);
        });
      } else {
        // Display a message when there is no appointment data
        var emptyRow = table.insertRow(1);
        var emptyCell = emptyRow.insertCell(0);
        emptyCell.colSpan = "3";
        emptyCell.textContent = "No appointments found.";
      }
    }
  };
  
  var method = "method=" + "fetchAllAppointment";
  xmlhttp.open("GET", "manage appointment.php?" + method, true);
  xmlhttp.send();
}

function fetchClinicCap(){
  var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {

            try{
                clinicCap = JSON.parse(this.responseText);
                //alert(this.responseText);
            }catch{
              alert(this.responseText);
            }
            document.getElementById("inputClinicCapacity").value = clinicCap.clinic_capacity;
        }
    };

    var method = "method=" + "fetchClinicCap";
    xmlhttp.open("GET", "manage clinic.php?" + method, true);
    xmlhttp.send();
}

function fetchCBQConfig() {
    //reloadMap();
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var cbqConfigData;

            try{
              if (this.responseText !== null && this.responseText !== "") {
                cbqConfigData = JSON.parse(this.responseText);
                //alert(cbqConfigData);
            } else {
                cbqConfigData = null;
            }
            }catch{
              alert(this.responseText);
            }
            //document.getElementById("inputClinicCapacity").value = cbqConfigData.clinic_capacity;
            var table = document.getElementById('displayCBQConfig');

            // Clear existing table rows
            while (table.rows.length > 1) {
                table.deleteRow(1);
            }

            // Populate the table with CBQ configuration data
            if (cbqConfigData != null && cbqConfigData.length > 0) {
                cbqConfigData.forEach(function(config, index) {
                    var row = table.insertRow(index + 1);
                    var presetCell = row.insertCell(0);
                    var xCell = row.insertCell(1);
                    var yCell = row.insertCell(2);
                    var minSupportCell = row.insertCell(3);
                    var maxSupportCell = row.insertCell(4);
                    var activeCell = row.insertCell(5);

                    presetCell.innerHTML = config.PRESET;
                    xCell.innerHTML = config.cbq_X;
                    yCell.innerHTML = config.cbq_Y;
                    minSupportCell.innerHTML = config.cbq_minSupport;
                    maxSupportCell.innerHTML = config.cbq_maxSupport;
                    activeCell.innerHTML = config.cbq_active;
                });
            } else {
                // Display a message when there is no CBQ configuration data
                var emptyRow = table.insertRow(1);
                var emptyCell = emptyRow.insertCell(0);
                emptyCell.colSpan = "6";
                emptyCell.textContent = "No CBQ configurations found.";
            }

            fetchPatientAVG();
        }
    };

    var method = "method=" + "fetchAllCBQConfig";
    xmlhttp.open("GET", "manage queue.php?" + method, true);
    xmlhttp.send();
}

function fetchPatientAVG() {
  //reloadMap();
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var array = this.responseText.split("?");
        document.getElementById("outputPatientPerDr").innerText = array[0];
        document.getElementById("outputDrCount").innerText = array[2];
        document.getElementById("outputQueueLength").innerText = array[1];
      }
  };

  var method = "method=" + "fetchPatientAverage";
  xmlhttp.open("GET", "manage queue.php?" + method, true);
  xmlhttp.send();
}

function progressQueue(){
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
        var response = xmlhttp.responseText;
        
        if(response != 0){
            fetchQueue();
        }
    }
    };

    var method = "method=" + "progressQueue";
    xmlhttp.open("GET", "manage queue.php?" + method, true);
    xmlhttp.send();
}

function dummyAppointment(){
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
        var response = xmlhttp.responseText;
        if(response == 1 || response === 1){
            fetchAppointment();
        }
    }
    };

    var method = "method=" + "dummyAppointment";
    xmlhttp.open("GET", "manage queue.php?" + method, true);
    xmlhttp.send();
}

function processIntoAPQ() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var response = xmlhttp.responseText;
            if (response == 1 || response === 1) {
                fetchQueue();
                fetchAppointment();
            }
        }
    };

    var method = "APPtoAPQ";
    var value = document.getElementById("inputDummyAPQ").value;

    var url = "manage queue.php";
    url += "?method=" + encodeURIComponent(method);
    url += "&value=" + encodeURIComponent(value);

    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

function reinsertIntoSLQ() {
  var xmlhttp = new XMLHttpRequest();
  if(globalCurrentDequeuedQueue == null || globalCurrentDequeuedQueue === null){
    alert("You are not currently in an active session with a patient!");
    return;
  }
  xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
          try{
            var responseJSON = JSON.parse(this.responseText);
            if(responseJSON.code == 1){
              alert(responseJSON.response);   
              fetchQueue();
            }
          }catch{
            alert(this.responseText);
          }
      }
  };

  var method = "reinsertIntoSLQ";
  //alert(globalCurrentDequeuedQueue.q_ID);  
  var url = "manage queue.php";
  url += "?method=" + encodeURIComponent(method);
  url += "&value1=" + encodeURIComponent(globalCurrentDequeuedQueue.q_ID);
  url += "&value2=" + encodeURIComponent(globalCurrentDequeuedQueue.patient_ICNum);

  xmlhttp.open("GET", url, true);
  xmlhttp.send();
}

function autofetchCurrentDequeuedPatient(){
  if(globalCurrentDequeuedPatient == null){
    alert("You currently are not in an active session with a patient!");
  }else{
    document.getElementById("inputPatientICNumApp").value=globalCurrentDequeuedPatient.patient_ICNum;
    alert("Loaded "+globalCurrentDequeuedPatient.patient_name+"'s IC Number!");
  }
}

function processCBQ() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var response = xmlhttp.responseText;
            if (response == 1 || response === 1) {
                fetchQueue();
                fetchAppointment();
            }
        }
    };

    var method = "stimulateCBQ";
    var value = document.getElementById("inputDummyCBQ").value;

    var url = "manage queue.php";
    url += "?method=" + encodeURIComponent(method);
    url += "&value=" + encodeURIComponent(value);

    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

function getQueuesLength() {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
          try{
            var responseJSON = JSON.parse(this.responseText);

          }catch(error){
            alert(this.responseText+", e:" + error);
          }
      }
  };

  var method = "getQueuesLength";

  var url = "manage queue.php";
  url += "?method=" + encodeURIComponent(method);

  xmlhttp.open("GET", url, true);
  xmlhttp.send();
}

function setClinicCapacity(){
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var response = xmlhttp.responseText;
            if (response == 1 || response === 1) {
                fetchCBQConfig();
            }
        }
    };

    var method = "setClinicCap";
    var value = document.getElementById("inputClinicCapacity").value;

    var url = "manage queue.php";
    url += "?method=" + encodeURIComponent(method);
    url += "&value=" + encodeURIComponent(value);

    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

function dummyGPQ(){
    var targetPatient = document.getElementById("inputInsertGPQPatientICNum").value;
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
        var response = xmlhttp.responseText;
        if(response == 1 || response === 1){
            //alert("inserted "+targetPatient);
            fetchQueue();
        }
    }
    };

    var method = "method=" + "dummyGPQ";
    var target = "target=" + targetPatient;
    xmlhttp.open("GET", "manage queue.php?" + method + "&" + target, true);
    xmlhttp.send();
}

function clearQueue(){
  if(!displayConfirmBox("Clear the whole queue? ALERT: THIS WILL WIPE THE WHOLE QUEUE!")){
    fetchQueue();
    return;
  }
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
        var response = xmlhttp.responseText;
        if(response == 1 || response === 1){
            fetchQueue();
        }
    }
    };

    var method = "method=" + "clearQueue";
    xmlhttp.open("GET", "manage queue.php?" + method, true);
    xmlhttp.send();
}

function clearTableRows(tableId) {
    var table = document.getElementById(tableId);
    // Clear existing table rows
    while (table.rows.length > 1) {
        table.deleteRow(1);
    }
}

function insertQueueData(data, tableId) {
    var table = document.getElementById(tableId);
    //alert(JSON.stringify(data));
    for (var i = 0; i < data.length; i++) {
        var row = table.insertRow();
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        var cell4 = row.insertCell(3);
        var cell5 = row.insertCell(4);
        var cell6 = row.insertCell(5);

        cell1.innerHTML = data[i].q_ID;
        cell2.innerHTML = data[i].q_before;
        cell3.innerHTML = data[i].q_after;
        cell4.innerHTML = data[i].q_type;
        cell5.innerHTML = data[i].patient_ICNum;
        var currentIC = data[i].patient_ICNum;
        // Create the button element for delete action
        var btnView = document.createElement("button");
        btnView.setAttribute("type", "button");
        btnView.setAttribute("class", "btn btn-outline-info");
        //alert("current ic insert: " + currentIC);
        btnView.onclick = function(ic) {
          return function() {
            //alert("current ic: " + ic);
            displayPatientInfo(ic);
          };
        }(currentIC);

        var viewIcon = document.createElement("i");
        viewIcon.classList.add("bi");
        viewIcon.classList.add("bi-info-circle");
        btnView.appendChild(viewIcon);

        cell6.appendChild(btnView);
    }
}

var floatingWindow = document.getElementById('myWindow');
var isDragging = false;
var startX;
var startY;
var windowOffsetX;
var windowOffsetY;

floatingWindow.addEventListener('mousedown', startDragging);
floatingWindow.addEventListener('touchstart', startDragging, { passive: false });

document.addEventListener('mousemove', dragWindow);
document.addEventListener('touchmove', dragWindow, { passive: false });

document.addEventListener('mouseup', stopDragging);
document.addEventListener('touchend', stopDragging);

var closeBtn = floatingWindow.querySelector('.window-close-btn');
closeBtn.addEventListener('click', function () {
  floatingWindow.style.display = 'none';
});

var attendSwitch = document.getElementById('attendSwitch');
var attendSwitchStatus = document.getElementById('attendSwitchStatus');

// Dynamically set window size based on screen size
function setWindowSize() {
  var screenWidth = window.innerWidth;
  var screenHeight = window.innerHeight;
  var windowWidth = Math.min(0.8 * screenWidth, 400);
  var windowHeight = Math.min(0.8 * screenHeight, 328);

  floatingWindow.style.width = windowWidth + 'px';
  //floatingWindow.style.height = windowHeight + 'px';
}

// Call setWindowSize initially and on window resize
setWindowSize();
window.addEventListener('resize', setWindowSize);

// Functions for dragging the window
function startDragging(e) {
  if (e.target.tagName.toLowerCase() === 'input') {
    return; // Ignore dragging when clicking inside an input field
  }
  e.preventDefault();
  isDragging = true;
  if (e.type === 'touchstart') {
    startX = e.touches[0].clientX;
    startY = e.touches[0].clientY;
  } else {
    startX = e.clientX;
    startY = e.clientY;
  }
  windowOffsetX = floatingWindow.offsetLeft;
  windowOffsetY = floatingWindow.offsetTop;
}

function dragWindow(e) {
  if (!isDragging) return;
  e.preventDefault();
  var x, y;
  if (e.type === 'touchmove') {
    x = e.touches[0].clientX;
    y = e.touches[0].clientY;
  } else {
    x = e.clientX;
    y = e.clientY;
  }
  var deltaX = x - startX;
  var deltaY = y - startY;
  var windowX = windowOffsetX + deltaX;
  var windowY = windowOffsetY + deltaY;

  windowX = Math.max(0, windowX);
  windowY = Math.max(0, windowY);

  floatingWindow.style.left = windowX + 'px';
  floatingWindow.style.top = windowY + 'px';
}


function stopDragging() {
  isDragging = false;
}

async function toggleAttend(status) {
  return new Promise(resolve => {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var response = xmlhttp.responseText;
        if (response == 1 || response === 1) {
          alert("Toggled your attendance!");
          fetchQueue();
          resolve(); // Resolving the promise
        } else {
          alert("Error setting your attendance!" + this.responseText);
          resolve(); // Resolving the promise even in case of an error
        }
      }
    };

    var method = "method=" + "setAttend";
    var target = "target=" + globalPersonnelID;
    var intent = "val=" + status;
    xmlhttp.open("GET", "manage personnel.php?" + method + "&" + target + "&" + intent, true);
    xmlhttp.send();
  });
}

//attend switch
var attendSwitch = document.getElementById('attendSwitch');

// Add onchange event listener to the switch checkbox
attendSwitch.addEventListener('change', async function(event) {
  var attendStatus = document.getElementById("attendSwitchStatus");

  if (event.target.checked) {
    // Checkbox is checked

    await toggleAttend("T"); // Wait for the attendance toggle

    attendStatus.innerHTML = "Attending";
    document.getElementById('windowHeader').classList.add("window-header-active");
    document.getElementById('windowHeaderText').classList.add("window-title-active");
  } else {
    // Checkbox is unchecked
    await toggleAttend("F"); // Wait for the attendance toggle

    attendStatus.innerHTML = "Idle";
    document.getElementById('windowHeader').classList.remove("window-header-active");
    document.getElementById('windowHeaderText').classList.remove("window-title-active");
  }
});

var globalCurrentDequeuedPatient = null;
var globalCurrentDequeuedQueue = null;

function dequeuePatient(){
    var xmlhttp = new XMLHttpRequest();
    if(globalCurrentDequeuedPatient != null){
      alert("Please conclude your currently active session firsr!");
      return;
    }
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var response = xmlhttp.responseText;
            try{
              var returnedVals = JSON.parse(response);
              var returnedPatient = JSON.parse(returnedVals.patient);
              var returnedQueue = JSON.parse(returnedVals.queue);
              document.getElementById("displayQueueID").value = returnedQueue.q_ID;
              document.getElementById("displayQueueName").value = returnedPatient.patient_name;
              document.getElementById("displayQueueGender").value = returnedPatient.patient_gender;
              document.getElementById("displayQueueAge").value = returnedPatient.patient_age;
              globalCurrentDequeuedPatient = returnedPatient;
              globalCurrentDequeuedQueue = returnedQueue;
              //alert(globalCurrentDequeuedQueue.patient_ICNum);
              fetchQueue();
            }catch{
              alert(this.responseText);
            }

        }
    };

    var method = "dequeuePatient";

    var url = "manage queue.php";
    url += "?method=" + encodeURIComponent(method);

    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

function concludePatient(){
  if(globalCurrentDequeuedPatient != null){
    if(displayConfirmBox("Are you concluding your session with " + globalCurrentDequeuedPatient.patient_name+"?")){
      document.getElementById("displayQueueID").value = "";
      document.getElementById("displayQueueName").value = "";
      document.getElementById("displayQueueGender").value = "";
      document.getElementById("displayQueueAge").value = "";
      globalCurrentDequeuedPatient = null;
    }
  }else{
    alert("You currently have no active session with a patient!");
  }
}

function login() {
    var loginID = document.getElementById("login_id").value;
    var loginICNum = document.getElementById("login_icnum").value;
    document.getElementById("loginFeedback").innerText = "";
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var response = xmlhttp.responseText;
            if (response == 0) {
                alert("Invalid credentials!");
                document.getElementById("loginFeedback").innerText = "Invalid username/password/both!";
            } else {
                alert("Login success!");
                
                var personnel = JSON.parse(response);
                sessionStorage.setItem('staffID', personnel.personnel_ID);
                globalPersonnelID = personnel.personnel_ID;
                globalPersonnelName = personnel.personnel_name;
                toggleColumnVisibility("block-login", 0);
                toggleColumnVisibility("patientColumn", 1);
                toggleColumnVisibility("personnelColumn", 1);
                toggleColumnVisibility("departmentColumn", 1);
                toggleColumnVisibility("serviceColumn", 1);
                toggleColumnVisibility("appointmentColumn", 1);
                toggleColumnVisibility("queueColumn", 1);
                toggleColumnVisibility("cbqConfigColumn", 1);
                document.getElementById("myWindow").style.display = "";
                // Handle successful login and personnel data here
            }
        }
    };

    var method = "checkStaff";
    var id = loginID;
    var email = ""; // Set the email value here

    var url = "manage personnel.php";
    url += "?method=" + encodeURIComponent(method);
    url += "&id=" + encodeURIComponent(id);
    url += "&IC=" + encodeURIComponent(loginICNum);

    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

function logout(){
  if(displayConfirmBox("Would you like to signoff?")){
    sessionStorage.removeItem('staffID');
    timedAlert("Signed out! Refreshing in 3s...", 3000);
    setTimeout(function() {
      location.reload();
    }, 3000);
  }
}

function timedAlert(message, duration) {
  // Show the alert
  alert(message);

  // Set a timeout to close the alert after the specified duration
  setTimeout(function() {
    // Close the alert (you may replace this with a custom function to hide the alert)
    alert.okay();
  }, duration);
}


var globalPersonnelID, globalPersonnelName;
//STARTUP
//make sure ni bawah sekali
document.addEventListener('DOMContentLoaded', function() {
    //temporarily disable log in
    /*
    document.getElementById("attendSwitchStatus").innerHTML = "Idle";
    document.getElementById("attendSwitch").checked = false;
    toggleColumnVisibility("patientColumn", 0);
    toggleColumnVisibility("personnelColumn", 0);
    toggleColumnVisibility("departmentColumn", 0);
    toggleColumnVisibility("serviceColumn", 0);
    toggleColumnVisibility("appointmentColumn", 0);
    toggleColumnVisibility("queueColumn", 0);
    toggleColumnVisibility("cbqConfigColumn", 0);
    document.getElementById("myWindow").style.display = "none";*/
    var staffID = sessionStorage.getItem('staffID');
    if(staffID){
      document.getElementById("block-login").style.display = "none";
      globalPersonnelID = staffID;
    } 
    fetchService();
    fetchDepartment();
    fetchPersonnel();
    fetchPatient();
    fetchCBQConfig();
    fetchAppointment();
    fetchQueue();
    fetchClinicCap();
    document.getElementById("toggleCBQSectionButton").click();

    setTimeout(function() {
      var deleteBtns = document.getElementsByClassName('delete-btn');
      // Loop through each delete button and disable it
      for (var i = 0; i < deleteBtns.length; i++) {
        deleteBtns[i].disabled = true;
      }
    }, 1000);

    //temporary for testing

    setTimeout(function() {
      initMap();
      
    }, 5000);
  
});

function echoLatLng(){
  alert("crosshair latlng: "+crosshair.getLatLng()+". radius latlng: "+circle.getLatLng());
}

function isset(variable) {
  return typeof variable !== 'undefined' && variable !== null;
}


window.addEventListener('unload', function(event) {
  toggleAttend('F');
});

function toggleColumnVisibility(columnId, visibility) {
    var column = document.getElementById(columnId);
    
    if (column) {
      if (visibility === 1) {
        column.style.display = "block";
      } else if (visibility === 0) {
        column.style.display = "none";
      }
    }
  }

// Get the modal
var insertionModal = document.getElementById("insertionModal");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close");
var generalInsertButton = document.getElementById("generalInsertButton");
function showInsertionModal(type){
    var targetFormDiv;
    if(type == "patient"){
        targetFormDiv = document.getElementById("displayPatientForm");
        insertionModal.style.display = "block";
        generalInsertButton.value = "patient";
    }
    if(type == "appointment"){
        targetFormDiv = document.getElementById("displayAppointmentForm");
        insertionModal.style.display = "block";
        generalInsertButton.value = "appointment";
    }
    if(type =="personnel"){
        targetFormDiv = document.getElementById("displayPersonnelForm");
        insertionModal.style.display = "block";
        generalInsertButton.value = "personnel";
    }
    if(type =="department"){
        targetFormDiv = document.getElementById("displayDepartmentForm");
        insertionModal.style.display = "block";
        generalInsertButton.value = "department";
    }
    if(type =="service"){
        targetFormDiv = document.getElementById("displayServiceForm");
        insertionModal.style.display = "block";
        generalInsertButton.value = "service";
    }

    targetFormDiv.classList.add("modal-form-active");
    
}


var modificationModal = document.getElementById("modificationModal");
var generalModifyButton = document.getElementById("generalModifyButton");

function showModificationModal(type, key) {
  //selamatkan original PK dulu
  document.getElementById("updateFormSaveID").value = key;
  var targetFormDiv;
  if (type == "patient") {
    targetFormDiv = document.getElementById("displayPatientUpdateForm");
    modificationModal.style.display = "block";
    generalModifyButton.value = "patient";
    var foundObject = patientDataArray.find(function(item){
      return item.patient_ICNum === key;
    });
    //alert("object found: "+foundObject.patient_name);
    document.getElementById('inputUpdatePatientICNum').value = foundObject['patient_ICNum'];
    document.getElementById('inputUpdatePatientName').value = foundObject['patient_name'];
    document.getElementById('inputUpdatePatientPhoneNumber').value = foundObject['patient_phoneNum'];
    document.getElementById('inputUpdatePatientEmail').value = foundObject['patient_email'];
  }
  if (type == "appointment") {
    targetFormDiv = document.getElementById("displayAppointmentUpdateForm");
    modificationModal.style.display = "block";
    generalModifyButton.value = "appointment";

  }
  if (type == "personnel") {
    targetFormDiv = document.getElementById("displayPersonnelUpdateForm");
    modificationModal.style.display = "block";
    generalModifyButton.value = "personnel";
    var foundObject = personnelDataArray.find(function(item){
      return item.personnel_ID === key;
    });
    //alert("object found: "+foundObject.personnel_name);
    document.getElementById('inputUpdatePersonnelName').value = foundObject['personnel_name'];
    document.getElementById('inputUpdatePersonnelICNumber').value = foundObject['personnel_ICNum'];
    document.getElementById('inputUpdatePersonnelPhoneNumber').value = foundObject['personnel_phoneNum'];
    document.getElementById('inputUpdatePersonnelEmail').value = foundObject['personnel_email'];
    document.getElementById('inputUpdatePersonnelType').value = foundObject['personnel_type'];

    var departmentDropdown = document.getElementById('departmentDropdownUpdatePersonnel');
    var departmentValue = foundObject['dept_code'];

    for (var i = 0; i < departmentDropdown.options.length; i++) {
      var option = departmentDropdown.options[i];
      if (option.value === departmentValue) {
        option.selected = true; // Set the selected attribute
        break; // Exit the loop since the option is found
      }
    }
  }
  if (type == "department") {
    targetFormDiv = document.getElementById("displayDepartmentUpdateForm");
    modificationModal.style.display = "block";
    generalModifyButton.value = "department";
    var foundObject = departmentDataArray.find(function(item){
      return item.dept_code === key;
    });
    //alert("object found: "+foundObject.dept_desc);
    document.getElementById('inputUpdateDepartmentCode').value = foundObject['dept_code'];
    document.getElementById('inputUpdateDepartmentName').value = foundObject['dept_name'];
    document.getElementById('inputUpdateDepartmentDesc').value = foundObject['dept_desc'];
  }
  if (type == "service") {
    targetFormDiv = document.getElementById("displayServiceUpdateForm");
    modificationModal.style.display = "block";
    generalModifyButton.value = "service";
    var foundObject = serviceDataArray.find(function(item){
      return item.svc_code === key;
    });
    //alert("object found: "+foundObject.dept_code);
    document.getElementById('inputUpdateServiceCode').value = foundObject['svc_code'];
    document.getElementById('inputUpdateServiceDesc').value = foundObject['svc_desc'];

    var departmentDropdown = document.getElementById('departmentDropdownUpdateService');
    var departmentValue = foundObject['dept_code'];

    for (var i = 0; i < departmentDropdown.options.length; i++) {
      var option = departmentDropdown.options[i];
      if (option.value === departmentValue) {
        option.selected = true; // Set the selected attribute
        break; // Exit the loop since the option is found
      }
    }
  }

  targetFormDiv.classList.add("modal-form-active");
}

// When the user clicks on <span> (x), close the modal
var span1 = document.querySelectorAll(".close");
span1.forEach((span)=>{
  span.onclick = function() {
    insertionModal.style.display = "none";
    modificationModal.style.display = "none";
    const modalForms = document.querySelectorAll(".modal-form");
    modalForms.forEach((modalForm) => {
        modalForm.classList.remove("modal-form-active");
    });

}
})


// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == insertionModal || event.target == modificationModal || event.target == patientDetailModal) {
    insertionModal.style.display = "none";
    modificationModal.style.display = "none";
    patientDetailModal.style.display = "none";
    const modalForms = document.querySelectorAll(".modal-form");
    modalForms.forEach((modalForm) => {
        modalForm.classList.remove("modal-form-active");
    });
    }
}

function displayConfirmBox(prompt) {
    if (confirm(prompt) == true) {
        return true;
    } else {
        return false;
    }
}

function validateAppointmentForm() {
    var inputDate = document.getElementById("inputDateApp").value;
    var inputTime = document.getElementById("inputTimeApp").value;
  
    var currentDate = new Date();
    var tomorrow = new Date();
    tomorrow.setDate(currentDate.getDate() + 1);
    tomorrow.setHours(0, 0, 0, 0); // Set time to midnight for comparison
  
    var selectedDate = new Date(inputDate);
    var selectedTime = new Date("2000-01-01T" + inputTime + ":00"); // Create a dummy date for time comparison
  
    if (selectedDate < tomorrow) {
      alert("Please select a date starting from tomorrow.");
      return false;
    }
  
    if (selectedTime < new Date("2000-01-01T08:00:00") || selectedTime > new Date("2000-01-01T16:00:00")) {
      alert("Please select a time between 8:00 AM and 4:00 PM.");
      return false;
    }
  
    // Validation passed, submit the form or perform further actions
    return true;
}

function isMalaysiaICNumber(icNumber) {
    // Remove any whitespace from the input string
    icNumber = icNumber.replace(/\s/g, '');
  
    // Regular expression pattern to match Malaysia IC number format
    var pattern = /^\d{6}-\d{2}-\d{4}$/;
  
    // Check if the input string matches the pattern
    return pattern.test(icNumber);
  }

  function isValidEmail(email) {
    // Regular expression pattern to validate email format
    var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  
    // Check if the input string matches the pattern
    return pattern.test(email);
  }

  function isValidPhoneNumber(phoneNumber) {
    // Remove spaces and symbols from the phone number
    var cleanedNumber = phoneNumber.replace(/\s+|[-+]/g, '');
  
    // Check if the cleaned number has 10 digits or more
    return cleanedNumber.length >= 10;
  }


  function generalInsertion() {
    var xmlhttp = new XMLHttpRequest();
    var targetField = generalInsertButton.value;
    var targetFeedbackField = document.getElementById("feedbackSpace");
    targetFeedbackField.innerHTML = "";
    var flag = true;
    var params = "";
  
    if (targetField === "patient") {
      // Fetch values for patient section
      // Perform validations (if required)
      var patient_ICNum = document.getElementById("inputPatientICNum").value;
      var patient_name = document.getElementById("inputPatientName").value;
      var patient_phoneNum = document.getElementById("inputPatientPhoneNumber").value;
      var patient_email = document.getElementById("inputPatientEmail").value;
  
      if (!isMalaysiaICNumber(patient_ICNum)) {
        targetFeedbackField.innerHTML += "Invalid IC Number! Please check the length and include dashes (-). ";
        flag = false;
      }
  
      if (!isValidPhoneNumber(patient_phoneNum)) {
        targetFeedbackField.innerHTML += "Invalid phone number! Must be at least 10 digits. ";
        flag = false;
      }
  
      if (!isValidEmail(patient_email)) {
        targetFeedbackField.innerHTML += "Invalid email address! Please include (@) and (.com). ";
        flag = false;
      }
  
      // Build parameters for patient section
      params += "patient_ICNum=" + encodeURIComponent(patient_ICNum) + "&";
      params += "patient_name=" + encodeURIComponent(patient_name) + "&";
      params += "patient_phoneNum=" + encodeURIComponent(patient_phoneNum) + "&";
      params += "patient_email=" + encodeURIComponent(patient_email) + "&";
    }
  
    if (targetField === "appointment") {
      // Fetch values for appointment section
      // Perform validations (if required)
      var patient_ICNumApp = document.getElementById("inputPatientICNumApp").value;
      var serviceDropdownApp = document.getElementById("serviceDropdownApp").value;
      //alert("value of service: "+serviceDropdownApp);
      var inputDateApp = document.getElementById("inputDateApp").value;
      var inputTimeApp = document.getElementById("inputTimeApp").value;
  
      if (!isMalaysiaICNumber(patient_ICNumApp)) {
        targetFeedbackField.innerHTML += "Invalid IC Number! Please check the length and include dashes (-). ";
        flag = false;
      }

      if(!validateDateTime()){
        targetFeedbackField.innerHTML += "Invalid appointment date/time! Please check the clinic's operational hours too.";
        flag = false;
      }
  
      // Build parameters for appointment section
      params += "patient_ICNumApp=" + encodeURIComponent(patient_ICNumApp) + "&";
      params += "serviceDropdownApp=" + encodeURIComponent(serviceDropdownApp) + "&";
      params += "inputDateApp=" + encodeURIComponent(inputDateApp) + "&";
      params += "inputTimeApp=" + encodeURIComponent(inputTimeApp) + "&";
      params += "personnel_ID=" + encodeURIComponent(globalPersonnelID) + "&";
    }
  
    if (targetField === "personnel") {
      // Fetch values for personnel section
      // Perform validations (if required)
      var inputPersonnelName = document.getElementById("inputPersonnelName").value;
      var inputPersonnelICNumber = document.getElementById("inputPersonnelICNumber").value;
      var inputPersonnelPhoneNumber = document.getElementById("inputPersonnelPhoneNumber").value;
      var inputPersonnelEmail = document.getElementById("inputPersonnelEmail").value;
      var departmentDropdownPersonnel = document.getElementById("departmentDropdownPersonnel").value;
      var inputPersonnelType = document.getElementById("inputPersonnelType").value;
  
      if (!isMalaysiaICNumber(inputPersonnelICNumber)) {
        targetFeedbackField.innerHTML += "Invalid Personnel IC Number! Please include dashes (-). ";
        flag = false;
      }
  
      if (!isValidPhoneNumber(inputPersonnelPhoneNumber)) {
        targetFeedbackField.innerHTML += "Invalid phone number! Must be at least 10 digits. ";
        flag = false;
      }
  
      if (!isValidEmail(inputPersonnelEmail)) {
        targetFeedbackField.innerHTML += "Invalid email address! Please include (@) and (.com). ";
        flag = false;
      }
  
      // Build parameters for personnel section
      params += "inputPersonnelName=" + encodeURIComponent(inputPersonnelName) + "&";
      params += "inputPersonnelICNumber=" + encodeURIComponent(inputPersonnelICNumber) + "&";
      params += "inputPersonnelPhoneNumber=" + encodeURIComponent(inputPersonnelPhoneNumber) + "&";
      params += "inputPersonnelEmail=" + encodeURIComponent(inputPersonnelEmail) + "&";
      params += "departmentDropdownPersonnel=" + encodeURIComponent(departmentDropdownPersonnel) + "&";
      params += "inputPersonnelType=" + encodeURIComponent(inputPersonnelType) + "&";
    }
  
    if (targetField === "department") {
      // Fetch values for department section
      // Perform validations (if required)
      var inputDepartmentCode = document.getElementById("inputDepartmentCode").value;
      var inputDepartmentName = document.getElementById("inputDepartmentName").value;
      var inputDepartmentDesc = document.getElementById("inputDepartmentDesc").value;
  
      // Build parameters for department section
      params += "inputDepartmentCode=" + encodeURIComponent(inputDepartmentCode) + "&";
      params += "inputDepartmentName=" + encodeURIComponent(inputDepartmentName) + "&";
      params += "inputDepartmentDesc=" + encodeURIComponent(inputDepartmentDesc) + "&";
    }
  
    if (targetField === "service") {
      // Fetch values for service section
      // Perform validations (if required)
      var inputServiceCode = document.getElementById("inputServiceCode").value;
      var inputServiceDesc = document.getElementById("inputServiceDesc").value;
      var departmentDropdownService = document.getElementById("departmentDropdownService").value;
  
      // Build parameters for service section
      params += "inputServiceCode=" + encodeURIComponent(inputServiceCode) + "&";
      params += "inputServiceDesc=" + encodeURIComponent(inputServiceDesc) + "&";
      params += "departmentDropdownService=" + encodeURIComponent(departmentDropdownService) + "&";
    }
  
    if (flag == false) {
      alert("Invalid fields detected! Please check all form fields.");
      return;
    } else {
      if (!displayConfirmBox("Are you sure you want to insert the specified fields?")) {
        return;
      }
    }
  
    xmlhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        try{
            var response = JSON.parse(xmlhttp.responseText);
            var code = response.code;
            var response = response.response;
                fetchPatient();
                fetchAppointment();
                fetchDepartment();
                fetchPersonnel();
                fetchService();
        }catch{
            var response = this.responseText;
        }
        
        alert(response);
        // Handle the response here
      }
    };
  
    var method = "method=" + encodeURIComponent("generalInsertion");
    var targetFieldParam = "targetField=" + encodeURIComponent(targetField);
    var url = "admin.php?" + method + "&" + targetFieldParam + "&" + params;
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
  }

  function generalDeletion(type, primaryKey){
    var xmlhttp = new XMLHttpRequest();
    if(!displayConfirmBox("Delete '"+primaryKey+"' from "+type+"?"))
      return;
    xmlhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        try{
            var response = JSON.parse(xmlhttp.responseText);
            var code = response.code;
            var response = response.response;
                fetchPatient();
                fetchAppointment();
                fetchDepartment();
                fetchPersonnel();
                fetchService();
        }catch{
            var response = this.responseText;
        }
        
        alert(response);
        // Handle the response here
      }
    };
  
    var method = "method=" + encodeURIComponent("generalDeletion");
    var targetType = "type=" + encodeURIComponent(type);
    var targetInstance = "instance=" + encodeURIComponent(primaryKey);
    var url = "admin.php?" + method + "&" + targetType + "&" + targetInstance;
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
  }

  function generalModification(){
    var xmlhttp = new XMLHttpRequest();
    var targetField = generalModifyButton.value;
    var targetFeedbackField = document.getElementById("feedbackSpaceUpdate");
    targetFeedbackField.innerHTML = "";
    var flag = true;
    var params = "";
  
    if (targetField === "patient") {
      // Fetch values for patient section
      // Perform validations (if required)
      var patient_ICNum = document.getElementById("inputUpdatePatientICNum").value;
      var patient_name = document.getElementById("inputUpdatePatientName").value;
      var patient_phoneNum = document.getElementById("inputUpdatePatientPhoneNumber").value;
      var patient_email = document.getElementById("inputUpdatePatientEmail").value;
      //alert(patient_ICNum);
      if (!isMalaysiaICNumber(patient_ICNum)) {
        targetFeedbackField.innerHTML += "Invalid IC Number! Please check the length and include dashes (-). ";
        flag = false;
      }
    
      if (!isValidPhoneNumber(patient_phoneNum)) {
        targetFeedbackField.innerHTML += "Invalid phone number! Must be at least 10 digits. ";
        flag = false;
      }
    
      if (!isValidEmail(patient_email)) {
        targetFeedbackField.innerHTML += "Invalid email address! Please include (@) and (.com). ";
        flag = false;
      }
    
      // Build parameters for patient section
      params += "patient_ICNum=" + encodeURIComponent(patient_ICNum) + "&";
      params += "patient_name=" + encodeURIComponent(patient_name) + "&";
      params += "patient_phoneNum=" + encodeURIComponent(patient_phoneNum) + "&";
      params += "patient_email=" + encodeURIComponent(patient_email) + "&";
    }
    
    if (targetField === "appointment") {
      // Fetch values for appointment section
      // Perform validations (if required)
      var patient_ICNumApp = document.getElementById("inputUpdatePatientICNumApp").value;
      var serviceDropdownApp = document.getElementById("serviceDropdownUpdateApp").value;
      var inputDateApp = document.getElementById("inputUpdateDateApp").value;
      var inputTimeApp = document.getElementById("inputUpdateTimeApp").value;
    
      if (!isMalaysiaICNumber(patient_ICNumApp)) {
        targetFeedbackField.innerHTML += "Invalid IC Number! Please check the length and include dashes (-). ";
        flag = false;
      }
    
      // Build parameters for appointment section
      params += "patient_ICNumApp=" + encodeURIComponent(patient_ICNumApp) + "&";
      params += "serviceDropdownApp=" + encodeURIComponent(serviceDropdownApp) + "&";
      params += "inputDateApp=" + encodeURIComponent(inputDateApp) + "&";
      params += "inputTimeApp=" + encodeURIComponent(inputTimeApp) + "&";
    }
    
    if (targetField === "personnel") {
      // Fetch values for personnel section
      // Perform validations (if required)
      var inputPersonnelName = document.getElementById("inputUpdatePersonnelName").value;
      var inputPersonnelICNumber = document.getElementById("inputUpdatePersonnelICNumber").value;
      var inputPersonnelPhoneNumber = document.getElementById("inputUpdatePersonnelPhoneNumber").value;
      var inputPersonnelEmail = document.getElementById("inputUpdatePersonnelEmail").value;
      var departmentDropdownPersonnel = document.getElementById("departmentDropdownUpdatePersonnel").value;
      var inputUpdatePersonnelType = document.getElementById("inputUpdatePersonnelType").value;

      if (!isMalaysiaICNumber(inputPersonnelICNumber)) {
        targetFeedbackField.innerHTML += "Invalid Personnel IC Number! Please include dashes (-). ";
        flag = false;
      }
    
      if (!isValidPhoneNumber(inputPersonnelPhoneNumber)) {
        targetFeedbackField.innerHTML += "Invalid phone number! Must be at least 10 digits. ";
        flag = false;
      }
    
      if (!isValidEmail(inputPersonnelEmail)) {
        targetFeedbackField.innerHTML += "Invalid email address! Please include (@) and (.com). ";
        flag = false;
      }
    
      // Build parameters for personnel section
      params += "inputPersonnelName=" + encodeURIComponent(inputPersonnelName) + "&";
      params += "inputPersonnelICNumber=" + encodeURIComponent(inputPersonnelICNumber) + "&";
      params += "inputPersonnelPhoneNumber=" + encodeURIComponent(inputPersonnelPhoneNumber) + "&";
      params += "inputPersonnelEmail=" + encodeURIComponent(inputPersonnelEmail) + "&";
      params += "departmentDropdownPersonnel=" + encodeURIComponent(departmentDropdownPersonnel) + "&";
      params += "inputPersonnelType=" + encodeURIComponent(inputUpdatePersonnelType) + "&";
    }
    
    if (targetField === "department") {
      // Fetch values for department section
      // Perform validations (if required)
      var inputDepartmentCode = document.getElementById("inputUpdateDepartmentCode").value;
      var inputDepartmentName = document.getElementById("inputUpdateDepartmentName").value;
      var inputDepartmentDesc = document.getElementById("inputUpdateDepartmentDesc").value;
    
      // Build parameters for department section
      params += "inputDepartmentCode=" + encodeURIComponent(inputDepartmentCode) + "&";
      params += "inputDepartmentName=" + encodeURIComponent(inputDepartmentName) + "&";
      params += "inputDepartmentDesc=" + encodeURIComponent(inputDepartmentDesc) + "&";
    }
    
    if (targetField === "service") {
      // Fetch values for service section
      // Perform validations (if required)
      var inputServiceCode = document.getElementById("inputUpdateServiceCode").value;
      var inputServiceDesc = document.getElementById("inputUpdateServiceDesc").value;
      var departmentDropdownService = document.getElementById("departmentDropdownUpdateService").value;
    
      // Build parameters for service section
      params += "inputServiceCode=" + encodeURIComponent(inputServiceCode) + "&";
      params += "inputServiceDesc=" + encodeURIComponent(inputServiceDesc) + "&";
      params += "departmentDropdownService=" + encodeURIComponent(departmentDropdownService) + "&";
    }
    
  
    if (flag == false) {
      alert("Invalid fields detected! Please check all form fields.");
      return;
    } else {
      if (!displayConfirmBox("Are you sure you want to update the amended fields?")) {
        return;
      }
    }
  
    xmlhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        try{
            var response = JSON.parse(xmlhttp.responseText);
            var code = response.code;
            var response = response.response;
                fetchPatient();
                fetchAppointment();
                fetchDepartment();
                fetchPersonnel();
                fetchService();
        }catch{
            var response = this.responseText;
        }
        
        alert(response);
        // Handle the response here
      }
    };
  
    var method = "method=" + encodeURIComponent("generalModification");
    var targetFieldParam = "targetField=" + encodeURIComponent(targetField);
    var originalID = "originalID=" + encodeURIComponent(document.getElementById("updateFormSaveID").value);
    var url = "admin.php?" + method + "&" + targetFieldParam + "&" + params + "&" + originalID;
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
  }
  
  function validateDateTime() {
    var selectedDate = new Date(document.getElementById("inputDateApp").value);
    var selectedTime = document.getElementById("inputTimeApp").value;
  
    var currentDateTime = new Date();
    currentDateTime.setHours(0, 0, 0, 0); // Set current date to midnight for comparison
  
    // Check if the selected date is tomorrow or later
    if (selectedDate <= currentDateTime) {
      alert("Please select a date starting from tomorrow onwards");
      return false;
    }
  
    // Check if the selected time is between 8am and 4.30pm
    var selectedHour = parseInt(selectedTime.split(":")[0]);
    var selectedMinute = parseInt(selectedTime.split(":")[1]);
    var selectedDateTime = new Date(selectedDate);
    selectedDateTime.setHours(selectedHour, selectedMinute);
  
    if (selectedDateTime < currentDateTime || selectedHour < 8 || selectedHour > 16 || (selectedHour === 16 && selectedMinute > 30)) {
      alert("Please select a time between 8am and 4.30pm");
      return false;
    }
  
    return true;
  }

var patientDetailModal = document.getElementById("patientDetailModal");
function displayPatientInfo(targetIC){
  patientDetailModal.style.display = "block";
    var foundObject = patientDataArray.find(function(item){
      return item.patient_ICNum === targetIC;
    });
    document.getElementById('displayPatientName').value = foundObject['patient_name'];
    document.getElementById('displayPatientPhoneNumber').value = foundObject['patient_phoneNum'];
    document.getElementById('displayPatientEmail').value = foundObject['patient_email'];
    //alert(foundObject["patient_age"]);
    document.getElementById('displayPatientAge').value = foundObject['patient_age'];
    document.getElementById('displayPatientGender').value = foundObject['patient_gender'];
    document.getElementById("displayOnlyPatient").classList.add("modal-form-active");
}

var deleteSwitch = document.getElementById('deleteSwitch');
var deleteSwitchStatus = document.getElementById('deleteSwitchStatus');
  deleteSwitch.addEventListener('change', function() {
    if (deleteSwitch.checked) {
      var deleteBtns = document.getElementsByClassName('delete-btn');
      var cardmyconfig = document.getElementsByClassName('card-myconfig');
      for (var i = 0; i < deleteBtns.length; i++) {
        deleteBtns[i].disabled = false;
      }
      for (var i = 0; i < cardmyconfig.length; i++) {
        cardmyconfig[i].classList.add("red-border");
      }
      deleteSwitch.classList.add('red-switch');
      deleteSwitchStatus.classList.add('red-bold-text');
      floatingWindow.classList.add("red-border");
      //document.getElementById("windowHeader").classList.add("red-border");
    }else {
      deleteSwitch.classList.remove('red-switch');
      deleteSwitchStatus.classList.remove('red-bold-text');
      var deleteBtns = document.getElementsByClassName('delete-btn');
      var cardmyconfig = document.getElementsByClassName('card-myconfig');
      for (var i = 0; i < deleteBtns.length; i++) {
        deleteBtns[i].disabled = true;
      }
      for (var i = 0; i < cardmyconfig.length; i++) {
        cardmyconfig[i].classList.remove("red-border");
      }
      floatingWindow.classList.remove("red-border");
      //document.getElementById("windowHeader").classList.remove("red-border");

    }
  });

  function filterTableRows(tableId, filterValue) {
    var table = document.getElementById(tableId);
    var rows = table.getElementsByTagName('tr');
  
    for (var i = 1; i < rows.length; i++) { // Start from index 1 to exclude the header row
      var row = rows[i];
      var cells = row.getElementsByTagName('td');
      var shouldHideRow = true;
  
      for (var j = 1; j < cells.length; j++) { // Start from index 1 to exclude the first cell
        var cellValue = cells[j].textContent || cells[j].innerText;
        if (cellValue.toLowerCase().indexOf(filterValue.toLowerCase()) > -1) {
          shouldHideRow = false;
          break;
        }
      }
  
      if (shouldHideRow) {
        row.style.display = 'none'; // Hide the row
      } else {
        row.style.display = ''; // Unhide the row
      }
    }
  }
  
  function searchTable(tableId, searchString, includeFirstFlag = false) {
    // Get the table element
    var table = document.getElementById(tableId);
  
    // Get all the rows
    var rows = table.getElementsByTagName('tr');
  
    // Set the starting index for columns based on the includeFirstFlag value
    var columnIndexStart = includeFirstFlag ? 0 : 1;
  
    for (var i = 1; i < rows.length; i++) { // Start from index 1 to skip the header row
      var row = rows[i];
      var cells = row.getElementsByTagName('td');
      var matchFound = false;
  
      // Check each cell's content for the search string (case-insensitive)
      for (var j = columnIndexStart; j < cells.length; j++) {
        var cell = cells[j];
        var content = cell.textContent || cell.innerText;
  
        if (content.toLowerCase().includes(searchString.toLowerCase())) {
          matchFound = true;
          break;
        }
      }
  
      // Hide or show the row based on the match
      if (matchFound) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    }
  }
  

// Define the function to be called
function handleInputChange(type) {
  return function() {
    var searchString = this.value; // Get the current value of the input

    // Call your function here with the search string
    if (type == 'patient')
      searchTable('displayPatient', searchString);

    if (type == 'personnel')
      searchTable('displayPersonnel', searchString);

    if (type == 'department')
      searchTable('displayDepartment', searchString, true);

    if (type == 'service')
      searchTable('displayService', searchString, true);
  };
}

var searchBarPatient = document.getElementById('searchBarPatient');
searchBarPatient.onkeyup = handleInputChange('patient');

var searchBarPersonnel = document.getElementById('searchBarPersonnel');
searchBarPersonnel.onkeyup = handleInputChange('personnel');

var searchBarPersonnel = document.getElementById('searchBarDepartment');
searchBarPersonnel.onkeyup = handleInputChange('department');

var searchBarPersonnel = document.getElementById('searchBarService');
searchBarPersonnel.onkeyup = handleInputChange('service');

function refreshProgressQueue(){
  progressQueue();
  fetchQueue();
}