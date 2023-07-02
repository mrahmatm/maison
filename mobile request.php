<?php
    $auth_type = "PER";
    include 'connect.php';
    include 'class.php';

    $q = $_REQUEST["method"];
    //$q = "newAppointment";
    if ($q == "signUp") {
        // Process sign-up logic
        $patient_ICNum = $_REQUEST["patient_ICNum"];
        $patient_name = $_REQUEST["patient_name"];
        $patient_email = $_REQUEST["patient_email"];
        $patient_phoneNum = $_REQUEST["patient_phoneNum"];

        $create = new Patient($patient_ICNum, $patient_name, $patient_email, $patient_phoneNum);
        $status = $create->addPatient($conn);

        echo $status;
        
    }

    if($q == "logInEmail" || $q === "logInEmail"){
        $email = $_REQUEST["email"];
        $status = searchPatientByEmail($conn, $email);
        echo $status;
    }
    
    if($q == "fetchPatientAppointment"){
        $email = $_REQUEST["email"];
        //$email = "mrahmatm@gmail.com";
        $status = searchAppointmentByEmail($conn, $email);
        echo $status;
    }

    if($q == "fetchAppointmentTypes"){
        $status = fetchAllServiceInfo($conn);
        echo $status;
    }

    if($q == "newAppointment"){
        $email = $_REQUEST["email"];
        //$email = "mrahmatm@gmail.com";
        $datetime = $_REQUEST["datetime"];
        //$datetime = "2023-06-29 16:00";
        $selectedService = $_REQUEST["selectedService"];
        //$selectedService = "Daily checkups.";
        $status = insertNewPatientAppointment($conn, $email, $selectedService, $datetime);
        echo $status;
    }
?>
