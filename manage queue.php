<?php

    $auth_type = "PER";
    include 'connect.php';
    include 'class.php';
    $q = $_REQUEST["method"];
    //$q="setClinicCap";
    if(strcmp($q,  "fetchAllQueue") == 0){
        $result = fetchAllQueue($conn);

        $SLQJSON = $result[0];
        $APQJSON = $result[1];
        $GPQJSON = $result[2];
        refreshCBQPreset($conn);
        //echo json_encode([$SLQJSON, $APQJSON, $GPQJSON]);
        echo $SLQJSON."##@@!!".$APQJSON."##@@!!".$GPQJSON;
        //$test = readQueueInstance($conn, "GPQ");
        //var_dump($test->toJSON());
    }

    if(strcmp($q,  "progressQueue") == 0){
        $result = progressQueue($conn);
    
        echo $result;
    }

    if(strcmp($q, "dummyAppointment") == 0){
        $newQueue = new Queue("APP", "546218-51-8937");
        $year = 2023;
        $month = 10;
        $date = 15;
        $hours = 21;
        $minutes = 00;
        $sqlDatetime = convertToSqlDatetime($year, $month, $date, $hours, $minutes);
        insertAppQueue($conn, $newQueue, $sqlDatetime);
        refreshCBQPreset($conn);
        echo 1;
    }

    if(strcmp($q, "APPtoAPQ") == 0){
        $val = $_REQUEST["value"];
        APPtoAPQ($conn, $val);
        refreshCBQPreset($conn);
        echo 1;
    }

    if(strcmp($q, "dummyGPQ") == 0){
        $val = $_REQUEST["target"];
        $newQueue = new Queue("GPQ", $val);
        enqueue($conn, $newQueue);
        refreshCBQPreset($conn);
        echo 1;
    }

    if(strcmp($q, "clearQueue") == 0){
        $sql = "DELETE FROM queue WHERE q_type <> 'APP'";
        $pdo_statement = $conn->prepare($sql);
        refreshCBQPreset($conn);
        if($pdo_statement->execute()){
            echo 1;
        }else{
            echo 0;
        }
        
        
    }

    if (strcmp($q, "fetchAllCBQConfig") == 0) {
        $result = fetchCBQConfig($conn);
        
        if ($result !== null) {
            echo $result;
        } else {
            echo "No CBQ configurations found.";
        }
    }

    if(strcmp($q, "stimulateCBQ") == 0){
        refreshCBQPreset($conn);
        $newQueue = new Queue("GPQ", "546218-51-8937");
        $val = $_REQUEST["value"];
        $newQueue->setID($val);
        $result = insertLateCBQ($conn, $newQueue);
        refreshCBQPreset($conn);
        echo $result;
    }

    if(strcmp($q, "setClinicCap") == 0){
        $val = floatval($_REQUEST["value"]);
        $val1 = floatval($_REQUEST["value1"]);
        $status = setClinicCapacity($conn, $val, $val1);
        $status1 = refreshCBQPreset($conn);
        if($status == 1 && $status1 == 1)
            echo 1;
        else
            echo -1;
    }

    if(strcmp($q, "fetchPatientAverage") == 0){
        $appointmentLength = getQueueLengthFromInstance(readQueueInstance($conn, "APQ"));
        $generalLength = getQueueLengthFromInstance(readQueueInstance($conn, "GPQ"));
        $secondLevelLength = getQueueLengthFromInstance(readQueueInstance($conn, "SLQ"));
        //sum of all lengths
        $sum = $appointmentLength+$generalLength+$secondLevelLength+1;
        //return $sum;

        //get number of present doctors
        $presentDr = countAllPresentDr($conn);
        
        //calculate current crowd score = sum all patients queueing / number of doctors
        $tempVal = $sum/$presentDr;

        echo $tempVal."?".$sum."?".$presentDr;
    }

    if(strcmp($q, "dequeuePatient") == 0){
        progressQueue($conn);

        $code = 1;
        $response = "Dequeued patient!";

        $dequeuedQueue = dequeue($conn, "SLQ");

        if(!$dequeuedQueue instanceof Queue){
            $code = -1;
            $response = "Queue is empty!";
            $returnVal = array(
                'code' => $code,
                'response' => $response,
            );
            
            $jsonData = json_encode($returnVal);
            echo $jsonData;
            exit();
        }

        $sql = "SELECT * FROM patient WHERE patient_ICNum=:target";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute([':target'=>$dequeuedQueue->patient_ICNum]);
        
        
        if(!$result=$pdo_statement->fetch(PDO::FETCH_OBJ)){
            $code = -2;
            $response = "Queue is empty!";
            if($code == -1){
                $code = -3;
                $response += "Queue is empty! Error fetching patient!";
            }
        }
        
        
        $returnVal = array(
            'code' => $code,
            'response' => $response,
            'queue' => json_encode($dequeuedQueue),
            'patient' => json_encode($result)
        );

        if($code == 1){
            progressQueue($conn);
        }
        
        $jsonData = json_encode($returnVal);
        echo $jsonData;
    }

    if(strcmp($q, "getQueuesLength") == 0){
        $SLQInstance = readQueueInstance($conn, "SLQ");
        $APQInstance = readQueueInstance($conn, "APQ");
        $GPQInstance = readQueueInstance($conn, "GPQ");

        $SLQLength = getQueueLengthFromInstance($SLQInstance);
        $APQLength = getQueueLengthFromInstance($APQInstance);
        $GPQLength = getQueueLengthFromInstance($GPQInstance);

        $returnVal = array(
            'SLQ' => $SLQLength,
            'APQ' => $APQLength,
            'GPQ' => $GPQLength
        );
        
        $jsonData = json_encode($returnVal);
        echo $jsonData;
    }

    if(strcmp($q, "reinsertIntoSLQ") == 0){
        $code = 1;
        $response = "Dequeued patient!";
        $q_ID = $_REQUEST["value1"];
        $patient_ICNum = $_REQUEST["value2"];

        progressQueue($conn);
        
        $create = new Queue("SLQ", $patient_ICNum);
        $create->setID($q_ID);
        enqueue($conn, $create);

        $returnVal = array(
            'code' => $code,
            'response' => $response
        );
        
        $jsonData = json_encode($returnVal);
        echo $jsonData;
    }

?>