<!DOCTYPE html>
<html>
    <head>
        <title>Admin Dashboard</title>
        <link rel="stylesheet" type="text/css" href="style.css">
        <?php
        require "bootstrap.php";
        ?>
    </head>
    <body>
        <div class="container">
        <div class="column">
                <h2>Manage Queue</h2>
                <button id="refreshQueue" onclick="fetchQueue()" class="button">Refresh</button>
                
                <div class="display-table-container">
                    <h3>Second Level Queue</h3>
                    <table id="displayPatientSLQ" class="display-table">
                    <tr>
                        <th>ID</th>
                        <th>Before</th>
                        <th>After</th>
                        <th>Type</th>
                        <th>IC</th>
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
                    </tr>
                    </table>
                </div>
            </div>

            <div class="column">
                <h2>Manage Appointment</h2>
                <button id="refreshAppointment" onclick="fetchAppointment()" class="button">Refresh</button>
                <div class="table-container">
                    <table id="displayAppointment"  class="display-table">
                        <tr>
                            <th>ID</th>
                            <th>Personnel ID</th>
                            <th>Date Time</th>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="column">
                <h2>Manage Patient</h2>
                <button id="refreshPatient" onclick="fetchPatient()" class="button">Refresh</button>
                <button id="insertPatient" onclick="openModal()" class="button">Add Patient</button>
                <div class="table-container">
                    <table id="displayPatient"  class="display-table">
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>IC</th>
                            <th>Gender</th>
                            <th>Age</th>
                            <th>Email</th>
                            <th>Phone</th>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="column">
                <h2>Manage Personnel</h2>
                <button id="refreshPersonnel" onclick="fetchPersonnel()" class="button">Refresh</button>
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
                        </tr>
                    </table>
                </div>
            </div>
            <div class="column">
                <h2>Manage Department</h2>
                <button id="refreshDepartment" onclick="fetchDepartment()" class="button">Refresh</button>
                <div class="table-container">
                    <table id="displayDepartment" class="display-table">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Desc</th>
                            <th>Count</th>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="column">
                <h2>Manage Services</h2>
                <button id="refreshService" onclick="fetchService()" class="button">Refresh</button>
                <div class="table-container">
                    <table id="displayService" class="display-table">
                        <tr>
                            <th>Code</th>
                            <th>Desc</th>
                            <th>Fee</th>
                            <th>Dept</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Container -->
        <div id="myModal" class="modal">
            <!-- Modal Content -->
            <div class="modal-content">
                <span class="close">&times;</span>
                <h3>Modal Title</h3>
                <p>Modal content goes here...</p>
            </div>
        </div>
    </div>
    <script src="script.js"></script>

    </body>
</html>
