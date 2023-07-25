<?php
    $auth_type = "PER";
    include 'connect.php';
    include 'class.php';

    $q = $_REQUEST["method"];
    //$q = "checkLocation";
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
        $email = $_REQUEST["patient_email"];
        $ICNum = $_REQUEST["patient_ICNum"];
        $status = searchPatientByEmail($conn, $email);
        $status1 = searchPatientByICNum($conn, $ICNum);
        if($status == 1 && $status1 == 1){
            echo 1;
        }else{
            echo 0;
        }
    }

    if($q == "fetchPatientAppointment"){
        $email = $_REQUEST["patient_email"];
        $status = searchAppointmentByEmail($conn, $email);
        echo $status;
    }

    if($q == "fetchAppointmentTypes"){
        $status = fetchAllServiceInfo($conn);
        echo $status;
    }

    if($q == "newAppointment"){
        $email = $_REQUEST["patient_email"];
        $datetime = $_REQUEST["app_datetime"];
        $selectedService = $_REQUEST["svc_code"];
        $status = insertNewPatientAppointment($conn, $email, $selectedService, $datetime);
        echo $status;
    }

    if($q == "checkLocation"){
        $userLat = $_REQUEST["latitude"];
        $userLng = $_REQUEST["longitude"];

        $userLatLng = $userLat.",".$userLng;
        $inRange = checkDistance($conn, $userLatLng);

        echo $inRange;
    }

    if($q == "queueWalkIn"){
        $email = $_REQUEST["patient_email"];
        //$email = "mrahmatm@gmail.com";
    
        try{
            $patient = fetchPatientByEmail($conn, $email);
            $create = new Queue("GPQ", $patient->patient_ICNum);
            $create = checkResetQueueID($conn, $create);
            enqueue($conn, $create);
            echo 1;
        }catch (Exception $e){
            echo 0;
        }
    }

    if($q == "peopleInFront"){
        //$q_ID = $_REQUEST["queueID"];
        $email = $_REQUEST["patient_email"];
        $patient = fetchPatientByEmail($conn, $email);
        //$email = "mrahmatm@gmail.com";
        //$q_ID = "977";

        try{
            $sql = "SELECT * FROM queue WHERE patient_ICNum=:target AND q_type<>'APP'";
            $pdo_statement = $conn->prepare($sql);
            $pdo_statement->execute([':target' =>$patient->patient_ICNum]);
            $result = $pdo_statement->fetch(PDO::FETCH_OBJ);
        }catch(PDOException $e){
            echo -1;
            exit();
        }
        $q_ID=$result->q_ID;
        //return (var_dump($result));
        $peopleLeft = 0;
        progressQueue($conn);
        if($result->q_type == "GPQ" || $result->q_type === "GPQ"){
            //echo "got intro gpq";
            //gpq, so kira dari depan
            $SLQ = readQueueInstance($conn, "SLQ");
            if($SLQ instanceof LinkedList){
                $peopleLeft+=traverseLinkedList($SLQ);
            }
            $APQ = readQueueInstance($conn, "APQ");
            if($APQ instanceof LinkedList){
                $peopleLeft+=traverseLinkedList($APQ);
            }
            
            $GPQ = readQueueInstance($conn, "GPQ");
            if($GPQ instanceof LinkedList){
                $peopleLeft+=traverseLinkedList($GPQ, $q_ID);
            }

            $peopleLeft -= 1;
        }elseif($result->q_type == "APQ" || $result->q_type === "APQ"){
            //echo "got intro apq";
            //apq, so kira dari depan, gpq belakang so x kira
            $SLQ = readQueueInstance($conn, "SLQ");
            if($SLQ instanceof LinkedList){
                $peopleLeft+=traverseLinkedList($SLQ);
            }
            $APQ = readQueueInstance($conn, "APQ");
            if($APQ instanceof LinkedList){
                $peopleLeft+=traverseLinkedList($APQ, $q_ID);
            }

            $peopleLeft -= 1;
        }elseif($result->q_type == "SLQ" || $result->q_type === "SLQ"){
            //echo "got intro slq";
            //slq , so kira dari depan, apq dgn gpq belakang so x kira
            $SLQ = readQueueInstance($conn, "SLQ");
            //echo var_dump($SLQ);
            if($SLQ instanceof LinkedList){
                $peopleLeft+=traverseLinkedList($SLQ, $q_ID);
                //echo var_dump($peopleLeft);
            }
            $peopleLeft -= 1;
            //echo var_dump($peopleLeft);
        }else{
            $peopleLeft = -1;
        }
        //return (var_dump($peopleLeft));
       // $SLQ = readQueueInstance($conn, "SLQ");
        echo $peopleLeft;
    }

    //check je
    if($q == "checkAppointment"){
        $email = $_REQUEST["patient_email"];
        $userDatetime = $_REQUEST["datetime"];
    
        $patient = fetchPatientByEmail($conn, $email);
        $patientIC = $patient->patient_ICNum;
    
        // Fetch all appointments for the day for the patient
        $sql = "SELECT * FROM queue WHERE q_type='APP' AND patient_ICNum=:target";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute([':target' => $patientIC]);
        $queueApp = $pdo_statement->fetchAll(PDO::FETCH_OBJ);
    
        $sql = "SELECT * FROM clinic";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $resultClinic = $pdo_statement->fetch(PDO::FETCH_OBJ);
        $allowedEarly = $resultClinic->clinic_earlyTolerance;
        $allowedLate = $resultClinic->clinic_lateTolerance;
    
        $date = new DateTime($userDatetime, new DateTimeZone('Asia/Kuala_Lumpur'));
        $startString = "-" . $allowedEarly . " minutes";
        $startRange = $date->modify($startString)->format('Y-m-d H:i:s');
        $repairString = "+" . $allowedEarly . " minutes";
        $date->modify($repairString)->format('Y-m-d H:i:s');
        
        $endString = "+" . ($allowedLate * 2) . " minutes";
        $endRange = $date->modify($endString)->format('Y-m-d H:i:s');
        $repairString = "-" . ($allowedLate * 2) . " minutes";
        $date->modify($repairString)->format('Y-m-d H:i:s');
        //return (var_dump($date));

        $appointmentsOfDay = array();
        $closestAppointment = null; // Initialize the closest appointment to null
    
        $found = false;
    
        foreach ($queueApp as $currentApp) {
            $sql = "SELECT * FROM appointment WHERE q_ID=:q_target";
            $pdo_statement = $conn->prepare($sql);
            $pdo_statement->execute([':q_target' => $currentApp->q_ID]);
            $resultAppointments = $pdo_statement->fetchAll(PDO::FETCH_OBJ);
    
            foreach ($resultAppointments as $appointment) {
                $appointmentDateTime = new DateTime($appointment->app_datetime);
    
                // Check if the appointment is on the same date as the given date
                if ($appointmentDateTime->format('Y-m-d') === $date->format('Y-m-d')) {
                    $appointmentsOfDay[] = $appointment;
                    $found = true;
                }
            }
        }
    
        // Handle the status based on the result of the loop
        if ($found) {
            $closestAppointmentDiff = PHP_INT_MAX;
            $closestAppointment = null; // Initialize the closest appointment to null
            $testArray = array();

            foreach ($appointmentsOfDay as $appointment) {
                $appointmentDateTime = new DateTime($appointment->app_datetime, new DateTimeZone('Asia/Kuala_Lumpur'));
                $timeDiff = abs($date->getTimestamp() - $appointmentDateTime->getTimestamp());
                
                $jsonObject = array(
                    "datetime" => $appointmentDateTime,
                    "diff" => $timeDiff,
                );

                $testArray[] = $jsonObject;

                if ($timeDiff < $closestAppointmentDiff) {
                    $closestAppointmentDiff = $timeDiff;
                    $closestAppointment = $appointment;
                }
            }
            //return (var_dump($testArray));
            //return (var_dump($closestAppointment));
            // Check if the closest appointment is within the allowed early and late range
            $closestAppointmentDateTime = new DateTime($appointment->app_datetime, new DateTimeZone('Asia/Kuala_Lumpur'));
            $allowedEarlyTime = clone $closestAppointmentDateTime;
            $allowedEarlyTime->modify('-' . $allowedEarly . ' minutes');
            //return (var_dump($allowedEarlyTime));
            $allowedLateTime = clone $closestAppointmentDateTime;
            $allowedLateTime->modify('+' . $allowedLate . ' minutes');
            //return (var_dump($allowedLateTime));
            if ($date >= $allowedEarlyTime && $date <= $allowedLateTime) {
                // The closest appointment is within the allowed early and late range
                // Perform actions with $closestAppointment, such as booking it for the user
                //APPtoAPQ($conn, $closestAppointment->q_ID);
                //refreshCBQPreset($conn);
                $status = 1;
            } else if ($date < $allowedEarlyTime) {
                // The appointment is too early
                $status = -1;
            } else if ($date > $allowedLateTime) {
                // The appointment is too late, apply CBQ
                $status = -2;
            }
        } else {
            // No appointments found for the day
            $status = -3;
        }
    
        echo $status;
        
    }

    //transfer app to apq or cbq if any
    if ($q == "attemptQueueAppointment") {
        $email = $_REQUEST["patient_email"];
        $userDatetime = $_REQUEST["datetime"];
    
        $patient = fetchPatientByEmail($conn, $email);
        $patientIC = $patient->patient_ICNum;
    
        // Fetch all queue for the patient
        $sql = "SELECT * FROM queue WHERE q_type='APP' AND patient_ICNum=:target";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute([':target' => $patientIC]);
        $queueApp = $pdo_statement->fetchAll(PDO::FETCH_OBJ);
    
        $sql = "SELECT * FROM clinic";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $resultClinic = $pdo_statement->fetch(PDO::FETCH_OBJ);
        $allowedEarly = $resultClinic->clinic_earlyTolerance;
        $allowedLate = $resultClinic->clinic_lateTolerance;
    
        $date = new DateTime($userDatetime, new DateTimeZone('Asia/Kuala_Lumpur'));
        $startString = "-" . $allowedEarly . " minutes";
        $startRange = $date->modify($startString)->format('Y-m-d H:i:s');
        $repairString = "+" . $allowedEarly . " minutes";
        $date->modify($repairString)->format('Y-m-d H:i:s');
        
        $endString = "+" . ($allowedLate * 2) . " minutes";
        $endRange = $date->modify($endString)->format('Y-m-d H:i:s');
        $repairString = "-" . ($allowedLate * 2) . " minutes";
        $date->modify($repairString)->format('Y-m-d H:i:s');
        //return (var_dump($date));

        $appointmentsOfDay = array();
        $closestAppointment = null; // Initialize the closest appointment to null
    
        $found = false;
        //fetch all appointment for each of the queue, check if it is tiday
        foreach ($queueApp as $currentApp) {
            $sql = "SELECT * FROM appointment WHERE q_ID=:q_target";
            $pdo_statement = $conn->prepare($sql);
            $pdo_statement->execute([':q_target' => $currentApp->q_ID]);
            $resultAppointments = $pdo_statement->fetchAll(PDO::FETCH_OBJ);
    
            foreach ($resultAppointments as $appointment) {
                $appointmentDateTime = new DateTime($appointment->app_datetime);
    
                // Check if the appointment is on the same date as the given date
                if ($appointmentDateTime->format('Y-m-d') === $date->format('Y-m-d')) {
                    $appointmentsOfDay[] = $appointment;
                    $found = true;
                }
            }
        }
    
        //if there are something found, check whether it is queuqable
        if ($found) {
            $closestAppointmentDiff = PHP_INT_MAX;
            $closestAppointment = null; // Initialize the closest appointment to null
            $testArray = array();

            foreach ($appointmentsOfDay as $appointment) {
                $appointmentDateTime = new DateTime($appointment->app_datetime, new DateTimeZone('Asia/Kuala_Lumpur'));
                $timeDiff = abs($date->getTimestamp() - $appointmentDateTime->getTimestamp());
                
                $jsonObject = array(
                    "datetime" => $appointmentDateTime,
                    "diff" => $timeDiff,
                );

                $testArray[] = $jsonObject;

                if ($timeDiff < $closestAppointmentDiff) {
                    $closestAppointmentDiff = $timeDiff;
                    $closestAppointment = $appointment;
                }
            }
            //return (var_dump($testArray));
            //return (var_dump($closestAppointment));
            // Check if the closest appointment is within the allowed early and late range
            $closestAppointmentDateTime = new DateTime($appointment->app_datetime, new DateTimeZone('Asia/Kuala_Lumpur'));
            $allowedEarlyTime = clone $closestAppointmentDateTime;
            $allowedEarlyTime->modify('-' . $allowedEarly . ' minutes');
            //return (var_dump($allowedEarlyTime));
            $allowedLateTime = clone $closestAppointmentDateTime;
            $allowedLateTime->modify('+' . $allowedLate . ' minutes');
            //return (var_dump($allowedLateTime));
            if ($date >= $allowedEarlyTime && $date <= $allowedLateTime) {
                // The closest appointment is within the allowed early and late range
                // Perform actions with $closestAppointment, such as booking it for the user
                progressQueue($conn);
                APPtoAPQ($conn, $closestAppointment->q_ID);
                progressQueue($conn);
                refreshCBQPreset($conn);
                $status = 1;
            } else if ($date < $allowedEarlyTime) {
                // The appointment is too early
                $status = -1;
            } else if ($date > $allowedLateTime) {
                // The appointment is too late, apply CBQ
                $sql = "SELECT * FROM queue WHERE q_ID=:target";
                $pdo_statement = $conn->prepare($sql);
                $pdo_statement->execute([':target' => $closestAppointment->q_ID]);
                $lateQueue = $pdo_statement->fetch(PDO::FETCH_OBJ);
                //echo var_dump($lateQueue);
                $tempQueue = new Queue($lateQueue->q_type, $lateQueue->patient_ICNum, $lateQueue->svc_code);
                $tempQueue->setID($closestAppointment->q_ID);
                //echo var_dump($tempQueue);
                refreshCBQPreset($conn);
                progressQueue($conn);
                $tempStatus = insertLateCBQ($conn, $tempQueue);
                progressQueue($conn);
                refreshCBQPreset($conn);

                $status = -2;

                if($tempStatus != 1){
                    $status = -9;
                }

            }
        } else {
            // No appointments found for the day
            $status = -3;
        }
    
        echo $status;
    }
    
    
    if($q == "checkIfQueueing"){
        $email = $_REQUEST["patient_email"];
        //$email = "mrahmatm@gmail.com";
    
        $patient = fetchPatientByEmail($conn, $email);

        $result = checkQueueByIC($conn, $patient->patient_ICNum);
        if(is_object($result)){
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
        echo ($jsonResponse);
    }
?>
