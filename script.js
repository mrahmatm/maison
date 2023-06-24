function fetchPersonnel() {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                //response
                //document.getElementById("testOutput").innerHTML = this.responseText;
                //alert("function triggered");
                    var response = xmlhttp.responseText;
                    var personnelData = JSON.parse(response);
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
                    cellPresent.innerHTML = ''; // Set the Present column empty for now
                    });
        }
        };
        var method = "method="+"fetchAllPersonnel";
        xmlhttp.open("GET", "manage personnel.php?" + method, true);
        xmlhttp.send();
}

function fetchPatient() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var response = xmlhttp.responseText;
            var patientData = JSON.parse(response);
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
            });
        }
    };

    var method = "method=" + "fetchAllPatient";
    xmlhttp.open("GET", "manage patient.php?" + method, true);
    xmlhttp.send();
}

function fetchQueue() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var response = xmlhttp.responseText;
            var queueData = JSON.parse(response);

            // Clear existing table rows for SLQ
            clearTableRows("displayPatientSLQ");
            // Insert SLQ data if not empty
            if (queueData[0] !== 0) {
                var slqData = JSON.parse(queueData[0]);
                insertQueueData(slqData, "displayPatientSLQ");
            }

            // Clear existing table rows for APQ
            clearTableRows("displayPatientAPQ");
            // Insert APQ data if not empty
            if (queueData[1] !== 0) {
                var apqData = JSON.parse(queueData[1]);
                insertQueueData(apqData, "displayPatientAPQ");
            }

            // Clear existing table rows for GPQ
            clearTableRows("displayPatientGPQ");
            // Insert GPQ data if not empty
            if (queueData[2] !== 0) {
                var gpqData = JSON.parse(queueData[2]);
                insertQueueData(gpqData, "displayPatientGPQ");
            }
            
        }
    };

    var method = "method=" + "fetchAllQueue";
    xmlhttp.open("GET", "manage queue.php?" + method, true);
    xmlhttp.send();
}

function fetchDepartment() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var response = xmlhttp.responseText;
            var departmentData = JSON.parse(response);

            var table = document.getElementById('displayDepartment');

            // Clear existing table rows
            while (table.rows.length > 1) {
                table.deleteRow(1);
            }

            // Populate the table with department data
            if (departmentData != null && departmentData.length > 0) {
                for (var i = 0; i < departmentData.length; i++) {
                    var department = departmentData[i];

                    var row = table.insertRow(i + 1);
                    var codeCell = row.insertCell(0);
                    var nameCell = row.insertCell(1);
                    var descCell = row.insertCell(2);
                    var headCountCell = row.insertCell(3);

                    codeCell.innerHTML = department.dept_code;
                    nameCell.innerHTML = department.dept_name;
                    descCell.innerHTML = department.dept_desc;
                    headCountCell.innerHTML = department.dept_headCount;
                }
            } else {
                // Display a message when there is no department data
                var emptyRow = table.insertRow(1);
                var emptyCell = emptyRow.insertCell(0);
                emptyCell.colSpan = "4";
                emptyCell.textContent = "No departments found.";
            }
        }
    };

    var method = "method=" + "fetchAllDepartment";
    xmlhttp.open("GET", "manage department.php?" + method, true);
    xmlhttp.send();
}


function fetchService() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
        var response = xmlhttp.responseText;
        var serviceData = JSON.parse(response);

        var table = document.getElementById('displayService');

        // Clear existing table rows
        while (table.rows.length > 1) {
            table.deleteRow(1);
        }

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
            feeCell.innerHTML = service.svc_fee;
            deptCell.innerHTML = service.dept_code;
            });
        } else {
          // Display a message when there is no service data
            var emptyRow = table.insertRow(1);
            var emptyCell = emptyRow.insertCell(0);
            emptyCell.colSpan = "4";
            emptyCell.textContent = "No services found.";
        }
    }
    };

    var method = "method=" + "fetchAllService";
    xmlhttp.open("GET", "manage service.php?" + method, true);
    xmlhttp.send();
}

function fetchAppointment() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var response = xmlhttp.responseText;
        
        var appointmentData;
        if (response !== null && response !== "") {
          appointmentData = JSON.parse(response);
        } else {
          appointmentData = null;
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
  



function clearTableRows(tableId) {
    var table = document.getElementById(tableId);
    // Clear existing table rows
    while (table.rows.length > 1) {
        table.deleteRow(1);
    }
}

function insertQueueData(data, tableId) {
    var table = document.getElementById(tableId);

    for (var i = 0; i < data.length; i++) {
        var row = table.insertRow();
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        var cell4 = row.insertCell(3);
        var cell5 = row.insertCell(4);

        cell1.innerHTML = data[i].ID;
        cell2.innerHTML = data[i].Before;
        cell3.innerHTML = data[i].After;
        cell4.innerHTML = data[i].Type;
        cell5.innerHTML = data[i]["Patient ICNum"];
    }
}

function showModalInsertPatient(){

}

// Open the modal
function openModal() {
    var modal = document.getElementById("myModal");
    modal.style.display = "block";
}

// Close the modal
function closeModal() {
    var modal = document.getElementById("myModal");
    modal.style.display = "none";
}
// Get the modal container element
var modal = document.getElementById("myModal");

// Check if the modal element exists
if (modal) {
    // Get the close button element
    var closeBtn = modal.querySelector(".close");

    // Close the modal when the close button is clicked
    closeBtn.addEventListener("click", function() {
        modal.style.display = "none";
    });

    // Close the modal when user clicks outside of the modal content
    window.addEventListener("click", function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
}











