<?php
    $auth_type = "PER";
    include 'connect.php';
    include 'class.php';

    //$q = $_REQUEST["method"];
    $q = "checkLocation";
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
        $status = searchAppointmentByEmail($conn, $email);
        echo $status;
    }

    if($q == "fetchAppointmentTypes"){
        $status = fetchAllServiceInfo($conn);
        echo $status;
    }

    if($q == "newAppointment"){
        $email = $_REQUEST["email"];
        $datetime = $_REQUEST["datetime"];
        $selectedService = $_REQUEST["selectedService"];
        $status = insertNewPatientAppointment($conn, $email, $selectedService, $datetime);
        echo $status;
    }

    if($q == "checkLocation"){
        $userLat = $_REQUEST["latitude"];
        $userLng = $_REQUEST["longitude"];
        $email = $_REQUEST["email"];

        $userLatLng = $userLat.",".$userLng;
        $inRange = checkDistance($conn, $userLatLng);

        $appointment = searchAppointmentByEmail($conn, $email);
        $hasAppointment = 0;
        $decodedJson = json_decode($appointment);
        if($decodedJson != 0){
            $hasAppointment = 1;
        }

        $response = array(
            'inRange' => intval($inRange),
            'hasAppointment' => $hasAppointment
        );

        // Encode the response as JSON
        $jsonResponse = json_encode($response);

        // Send the JSON response back to the client
        echo $jsonResponse;
    }

    if($q == "queueWalkIn"){
        $email = $_REQUEST["email"];
        //$email = "mrahmatm@gmail.com";
    
        $patient = fetchPatientByEmail($conn, $email);

        $create = new Queue("GPQ", $patient->patient_ICNum);
        $create = checkResetQueueID($conn, $create);
        enqueue($conn, $create);
    
        $response = array(
            'isQueueing' => 1,
            'q_ID' => $create->q_ID
        );
    
        // Encode the response as JSON
        $jsonResponse = json_encode($response);
    
        // Send the JSON response back to the client
        echo $jsonResponse;
    }

    if($q == "sendQueueID"){
        $q_ID = $_REQUEST["queueID"];
        //$email = "mrahmatm@gmail.com";
        //$q_ID = "977";

        $sql = "SELECT * FROM queue WHERE q_ID=:q_target";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute([':q_target' => $q_ID]);
        $result = $pdo_statement->fetch(PDO::FETCH_OBJ);

        if($result == false){
            $response = array(
                'peopleInFront' => -1,
            );
        
            // Encode the response as JSON
            $jsonResponse = json_encode($response);
        
            // Send the JSON response back to the client
            echo $jsonResponse;
            exit();
        }

        $peopleLeft = 0;
        progressQueue($conn);
        if($result->q_type == "GPQ" || $result->q_type === "GPQ"){
            //gpq, so kira dari depan
            $SLQ = readQueueInstance($conn, "SLQ");
            if($SLQ instanceof LinkedList){
                $peopleLeft+=traverseLinkedList($SLQ, $q_ID);
            }
            $APQ = readQueueInstance($conn, "APQ");
            if($APQ instanceof LinkedList){
                $peopleLeft+=traverseLinkedList($APQ, $q_ID);
            }
            
            $GPQ = readQueueInstance($conn, "GPQ");
            if($GPQ instanceof LinkedList){
                $peopleLeft+=traverseLinkedList($GPQ, $q_ID);
            }

            $peopleLeft -= 1;
        }

        if($result->q_type == "APQ" || $result->q_type === "APQ"){
            //apq, so kira dari depan, gpq belakang so x kira
            $SLQ = readQueueInstance($conn, "SLQ");
            if($SLQ instanceof LinkedList){
                $peopleLeft+=traverseLinkedList($SLQ, $q_ID);
            }
            $APQ = readQueueInstance($conn, "APQ");
            if($APQ instanceof LinkedList){
                $peopleLeft+=traverseLinkedList($APQ, $q_ID);
            }

            $peopleLeft -= 1;
        }

        if($result->q_type == "SLQ" || $result->q_type === "SLQ"){
            //slq , so kira dari depan, apq dgn gpq belakang so x kira
            $GPQ = readQueueInstance($conn, "GPQ");
            if($GPQ instanceof LinkedList){
                $peopleLeft+=traverseLinkedList($GPQ, $q_ID);
            }
        }
        //echo "count: ".$peopleLeft;
        
        $response = array(
            'peopleInFront' => $peopleLeft,
        );
    
        // Encode the response as JSON
        $jsonResponse = json_encode($response);
    
        // Send the JSON response back to the client
        echo $jsonResponse;
    }

    if($q == "attemptQueueAppointment"){
        //$q_ID = $_REQUEST["queueID"];
        //$userDatetime = $_REQUEST["datetime"];
        $userDatetime = $_REQUEST["datetime"];
        //$email = "mrahmatm@gmail.com";
        //$q_ID = "086";
        //$userDatetime = "2023-07-04 10:59:00";

        $sql = "SELECT * FROM queue WHERE q_ID=:q_target";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute([':q_target' => $q_ID]);
        $result = $pdo_statement->fetch(PDO::FETCH_OBJ);

        $sql = "SELECT * FROM appointment WHERE q_ID=:q_target";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute([':q_target' => $q_ID]);
        $resultApp = $pdo_statement->fetch(PDO::FETCH_OBJ);

        $sql = "SELECT * FROM clinic ";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $resultClinic = $pdo_statement->fetch(PDO::FETCH_ASSOC);

        $appTime = $resultApp->app_datetime;
        $clinicEarly = $resultClinic["clinic_earlyTolerance"];
        $clinicLate = $resultClinic["clinic_lateTolerance"];
        
        $status = compareDatetime($userDatetime, $appTime, $clinicEarly, $clinicLate);

        $feedback = "";
        if ($status == -1) {
            $feedback = "You arrived too early for the appointment.";
        } elseif ($status == 0) {
            $feedback = "You arrived on time for the appointment.";
        } elseif ($status == 1) {
            $feedback = "You arrived late for the appointment.";
        } else {
            $feedback = "Invalid result.";
        }
        
        $response = array(
            'code' => $status,
            'response' => $feedback
        );
    
        // Encode the response as JSON
        $jsonResponse = json_encode($response);
    
        // Send the JSON response back to the client
        echo $jsonResponse;
    }

    if($q == "checkIfQueueing"){
        //$email = $_REQUEST["email"];
        $email = "mrahmatm@gmail.com";
    
        $patient = fetchPatientByEmail($conn, $email);

        $result = checkQueueByIC($conn, $patient->patient_ICNum);
        if($result != 0){
            $response = array(
                'isQueueing' => 1,
                'q_ID' => $result->q_ID
            );
        }else{
            $response = array(
                'isQueueing' => 0
            );
        }
    
        // Encode the response as JSON
        $jsonResponse = json_encode($response);
    
        // Send the JSON response back to the client
        echo $jsonResponse;
    }
?>
