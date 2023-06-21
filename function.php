<?php
    //include 'class.php';

    function deriveAgeFromIC($target){

        if(strlen($target) <= 0 || strlen($target) > 14)
            return -1;

        if(strpos($target, "-") == FALSE)
            return -1;
        
        $arr = explode("-", $target);
        $birthDate = $arr[0];
        
        $correctedDate = "";
        $correctedDate .= $birthDate[4];
        $correctedDate .= $birthDate[5];
        $correctedDate .= $birthDate[2];
        $correctedDate .= $birthDate[3];
        $correctedDate .= $birthDate[0];
        $correctedDate .= $birthDate[1];
        
        $targetYear = (int) substr($correctedDate, strlen($correctedDate)-2, 2);
        $currentYear = date("y");
        $intCurrentYear = (int) $currentYear;
        
        //kiv
        if ($intCurrentYear < $targetYear){
            $correctedDate = substr_replace($correctedDate, "19", strlen($correctedDate)-2, 0);
        }else{
            $correctedDate = substr_replace($correctedDate, "20", strlen($correctedDate)-2, 0);
        }

        $correctedDate = substr_replace($correctedDate, "-", 2, 0);
        $correctedDate = substr_replace($correctedDate, "-", 5, 0);

        $birthDate = date_create($correctedDate);
        $nowDate = date_create(date('d-M-Y'));
        $dayDiff = date_diff($birthDate, $nowDate);

        return $dayDiff->y;
    }

    function deriveGenderFromIC($target){
        if(strlen($target) <= 0 || strlen($target) > 14)
            return -1;

        if(strpos($target, "-") == FALSE)
            return -1;
        
        $arr = explode("-", $target);
        $targetSegment = $arr[2];
        $targetChar = substr($targetSegment, -1);
        $targetDigit = (int) $targetChar;

        if($targetDigit % 2 == 0){
            return 'F';
        }else{
            return 'M';
        }

    }

    function derivePersonnelID($dept_code, $dept_headCount){
        $nowYear = date('Y');
        $strYear = strftime('Y', $nowYear);
        $output = $dept_code."-".$nowYear."-".$dept_headCount+1;

        return $output;
    }

    function checkQueueExists($conn, $type){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }
        
        $pdo_statement = $conn->prepare("SELECT * FROM queue WHERE q_type=:target");
        $pdo_statement->execute([':target'=>$type]);
        $result = $pdo_statement->fetch(PDO::FETCH_LAZY);
        if($result != NULL)
            return 1;
        else
            return 0;
    }

    function readQueueInstance($conn, $type){
        if(checkQueueExists($conn, $type) == 1){
            $currentQueue = new LinkedList();
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }

            //cari head
            $sql = "SELECT * FROM queue WHERE q_type = :targetValue AND q_before IS NULL";
            $pdo_statement = $conn->prepare($sql);
            $pdo_statement->execute([':targetValue'=>$type]);
            $result=$pdo_statement->fetch(PDO::FETCH_OBJ);
            
            if($result->q_before === NULL && $result->q_after === NULL){
                $queue = new Queue($result->q_type, $result->patient_ICNum, $result->svc_code);
                $queue->setID($result->q_ID);
                $tempBefore = $result->q_before;
                $tempAfter = $result->q_after;
                $queue->setAfter($tempAfter);
                $queue->setBefore($tempBefore);
                $currentQueue->insertAtTail($queue);
                echo "<script>alert('Only 1 node available')</script>";
                return $currentQueue;
            }

            do{
                $queue = new Queue($result->q_type, $result->patient_ICNum, $result->svc_code);
                $queue->setID($result->q_ID);
                $tempBefore = $result->q_before;
                $tempAfter = $result->q_after;
                $queue->setAfter($tempAfter);
                $queue->setBefore($tempBefore);
                //echo "<script>alert('Current id, after: ".$queue->q_ID.", ".$queue->q_after."')</script>";
                //echo "<script>alert('Values of temps before, after: ".$tempBefore.", ".$tempAfter."')</script>";
                $currentQueue->insertAtTail($queue);
                //echo "<script>alert('Current head: ".$currentQueue->head->toString()."')</script>";
                $sql = "SELECT * FROM queue WHERE q_type=:targetValue AND q_id=:afterCurrent AND q_before=:currentQueue";
                $pdo_statement = $conn->prepare($sql);
                $pdo_statement->execute([':targetValue' => $type, ':afterCurrent' => $queue->q_after, ':currentQueue' => $queue->q_ID]);
                $result = $pdo_statement->fetch(PDO::FETCH_OBJ);
            }while($result != false);
            /*
            //cari tail
            $sql = "SELECT * FROM queue WHERE q_type = :targetValue AND q_after IS NULL";
            $pdo_statement = $conn->prepare($sql);
            $pdo_statement->execute([':targetValue'=>$type]);
            $result=$pdo_statement->fetch(PDO::FETCH_OBJ);
            $queue = new Queue($result->q_type, $result->patient_ICNum, $result->svc_code);
            $queue->setID($result->q_ID);
            $queue->setBefore($result->q_before);
            $currentQueue->insertAtTail($queue);
            */
            return $currentQueue  ;
        }else{
            return -1;
        }
    }

    function enqueue($conn, $queue){
        if(checkQueueExists($conn, $queue->q_type) == 1){
            //$currentQueue = new LinkedList();
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }
            $queue = checkResetQueueID($conn, $queue);
            $currentQueue = readQueueInstance($conn, $queue->q_type);
            $currentTail = $currentQueue->getTail();
            $queue->setBefore($currentTail->data->q_ID);
            $currentQueue->insertAtTail($queue);
            //return $currentQueue;
            
            $sql = "DELETE FROM queue WHERE q_type =:targetValue";
            $pdo_statement = $conn->prepare($sql);
            $status = $pdo_statement->execute([':targetValue'=>$queue->q_type]);

            $currentNode = $currentQueue->head;
            $flag = true;
            //return $queue->svc_code;
            while ($currentNode !== null) {
                $queue = $currentNode->data;
                $sql = "INSERT INTO queue (q_ID, q_before, q_after, q_type, patient_ICNum, svc_code) VALUES (:q_ID, :q_before, :q_after, :q_type, :patient_ICNum, :svc_code)";
                $pdo_statement = $conn->prepare($sql);
                $pdo_statement->bindValue(':q_ID', $queue->q_ID);
                $pdo_statement->bindValue(':q_before', $queue->q_before);
                $pdo_statement->bindValue(':q_after', $queue->q_after);
                $pdo_statement->bindValue(':q_type', $queue->q_type);
                $pdo_statement->bindValue(':patient_ICNum', $queue->patient_ICNum);
                $pdo_statement->bindValue(':svc_code', $queue->svc_code);
                $success = $pdo_statement->execute();
                $currentNode = $currentNode->after;
            }
        }else{
            $queue = checkResetQueueID($conn, $queue);
            $queue->setBefore(null);
            $queue->setAfter(null);
            $sql = "INSERT INTO queue (q_ID, q_before, q_after, q_type, patient_ICNum, svc_code) VALUES (:q_ID, :q_before, :q_after, :q_type, :patient_ICNum, :svc_code)";
                $pdo_statement = $conn->prepare($sql);
                $pdo_statement->bindValue(':q_ID', $queue->q_ID);
                $pdo_statement->bindValue(':q_before', $queue->q_before);
                $pdo_statement->bindValue(':q_after', $queue->q_after);
                $pdo_statement->bindValue(':q_type', $queue->q_type);
                $pdo_statement->bindValue(':patient_ICNum', $queue->patient_ICNum);
                $pdo_statement->bindValue(':svc_code', $queue->svc_code);
                $success = $pdo_statement->execute();
        }
    }

    function dequeue($conn, $queueType){
        if(checkQueueExists($conn, $queueType) == 1){
            //$currentQueue = new LinkedList();
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }
            $currentQueue = readQueueInstance($conn, $queueType);
            $removedNode = $currentQueue->removeHead();
            
            
            //replace current queue dengan updated queue
            $sql = "DELETE FROM queue WHERE q_type =:targetValue";
            $pdo_statement = $conn->prepare($sql);
            $status = $pdo_statement->execute([':targetValue'=>$queueType]);
            $currentNode = $currentQueue->head;
            $flag = true;
            //return $queue->svc_code;
            while ($currentNode !== null) {
                $queue = $currentNode->data;
                $sql = "INSERT INTO queue (q_ID, q_before, q_after, q_type, patient_ICNum, svc_code) VALUES (:q_ID, :q_before, :q_after, :q_type, :patient_ICNum, :svc_code)";
                $pdo_statement = $conn->prepare($sql);
                $pdo_statement->bindValue(':q_ID', $queue->q_ID);
                $pdo_statement->bindValue(':q_before', $queue->q_before);
                $pdo_statement->bindValue(':q_after', $queue->q_after);
                $pdo_statement->bindValue(':q_type', $queueType);
                $pdo_statement->bindValue(':patient_ICNum', $queue->patient_ICNum);
                $pdo_statement->bindValue(':svc_code', $queue->svc_code);
                $success = $pdo_statement->execute();
                $currentNode = $currentNode->after;
            }

            return $removedNode;
        }
    }

    //for late appointment patients only
    function insertLateCBQ($conn, $queue){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }
        if(checkQueueExists($conn, "GPQ") != 1){

        }
        refreshCBQPreset($conn);
        //get current length of general patient queue
        $generalLength = getQueueLengthFromInstance(readQueueInstance($conn, "GPQ"));

        //get current active preset
        $GPQ = readQueueInstance($conn, "GPQ");
        $sql = "SELECT PRESET FROM cbq WHERE cbq_active='T'";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $result=$pdo_statement->fetch(PDO::FETCH_LAZY);
        $activePreset = $result->PRESET;

        //get x and y values for the active preset
        $GPQ = readQueueInstance($conn, "GPQ");
        $sql = "SELECT cbq_X, cbq_Y FROM cbq WHERE PRESET=:targetPreset";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute(['targetPreset'=>$activePreset]);
        $result=$pdo_statement->fetch(PDO::FETCH_LAZY);
        $cbqX = $result->cbq_X;
        $cbqY = $result->cbq_Y;

        // Calculate the value of Y%
        $yValue = ($cbqY / 100) * $generalLength;

        // Calculate the Punishment Position using the derived value of Y%
        $punishmentPosition = ceil($cbqX + $yValue)-1;
        echo "<script>alert('".$punishmentPosition."');</script>";
        if($GPQ->insertAfter($queue, $punishmentPosition)){
            $queue = checkResetQueueID($conn, $queue);
            echo "<script>alert('current queue type: '".$queue->q_type.")</script>";
            $sql = "DELETE FROM queue WHERE q_type =:targetValue";
            $pdo_statement = $conn->prepare($sql);
            $status = $pdo_statement->execute([':targetValue'=>$queue->q_type]);

            $currentNode = $GPQ->head;
            $flag = true;
            //return $queue->svc_code;
            while ($currentNode !== null) {
                $queue = $currentNode->data;
                $sql = "INSERT INTO queue (q_ID, q_before, q_after, q_type, patient_ICNum, svc_code) VALUES (:q_ID, :q_before, :q_after, :q_type, :patient_ICNum, :svc_code)";
                $pdo_statement = $conn->prepare($sql);
                $pdo_statement->bindValue(':q_ID', $queue->q_ID);
                $pdo_statement->bindValue(':q_before', $queue->q_before);
                $pdo_statement->bindValue(':q_after', $queue->q_after);
                $pdo_statement->bindValue(':q_type', $queue->q_type);
                $pdo_statement->bindValue(':patient_ICNum', $queue->patient_ICNum);
                $pdo_statement->bindValue(':svc_code', $queue->svc_code);
                $success = $pdo_statement->execute();
                $currentNode = $currentNode->after;
            }
            return 1;
        }else{
            return -1;
        }

    }

    function checkResetQueueID($conn, $queue){
        $readQueue = $queue;
        while(true){
            $sql = "SELECT * FROM queue WHERE q_id=:targetValue AND q_type=:targetType";
            $pdo_statement = $conn->prepare($sql);
            $pdo_statement->execute([':targetValue'=>$readQueue->q_ID, ':targetType'=>$readQueue->q_type]);
            $result=$pdo_statement->fetch(PDO::FETCH_OBJ);
            if($result===false)
                break;
            else{
                echo "<script>alert('Redundant q_id, renegerating...')</script>";
                $readQueue->resetID();
            }
        }
        return $readQueue;
    }

    function countAllPresentDr($conn){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }

        $sql = "SELECT * FROM personnel WHERE personnel_type='DR' AND personnel_attend='T'";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $result=$pdo_statement->fetchAll();
        if($result != null){
                $numRows = $pdo_statement->rowCount();
                return $numRows+1;
            }else{
                return 0+1;
            }
    }

    function refreshCBQPreset($conn){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }

        //fetch set clinic capacity
        $sql = "SELECT clinic_capacity FROM clinic";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $result=$pdo_statement->fetch(PDO::FETCH_LAZY);
        $fullCapacity = $result->clinic_capacity;
        //echo "<script>alert('Current capacity: ".$fullCapacity."')</script>";
        //divide into 3 to receive estimate size for each of 3 quartile
        $quartile = ceil($fullCapacity/3);

        //update the minimum and maximum support of each preset of CBQ
        //low
        $pdo_statement = $conn->prepare("UPDATE cbq
                SET cbq_maxSupport = :quartile
                WHERE PRESET=:targetPreset");
        $pdo_statement->execute([':quartile'=>$quartile, ':targetPreset'=>'LOW']);
        //medium
        $pdo_statement = $conn->prepare("UPDATE cbq
                SET cbq_minSupport =:minQuartile, cbq_maxSupport =:maxQuartile
                WHERE PRESET=:targetPreset");
        $pdo_statement->execute([':minQuartile'=>$quartile+1, ':maxQuartile'=>$quartile+$quartile, ':targetPreset'=>'MEDIUM']);
        //high
        $pdo_statement = $conn->prepare("UPDATE cbq
                SET cbq_minSupport = :quartile
                WHERE PRESET=:targetPreset");
        $pdo_statement->execute([':quartile'=>$quartile+$quartile+1, ':targetPreset'=>'HIGH']);

        //get all queue's lengths
        $appointmentLength = getQueueLengthFromInstance(readQueueInstance($conn, "APQ"));
        $generalLength = getQueueLengthFromInstance(readQueueInstance($conn, "GPQ"));
        $secondLevelLength = getQueueLengthFromInstance(readQueueInstance($conn, "SLQ"));
        //sum of all lengths
        $sum = $appointmentLength+$generalLength+$secondLevelLength+1;
        //get number of present doctors
        $presentDr = countAllPresentDr($conn);
        //calculate current crowd score = sum all patients queueing / number of doctors
        $tempVal = $sum/$presentDr;
        //echo "<script>alert('Current score: ".$tempVal."')</script>";
        //set which CBQ preset to be used based on score
        $pdo_statement = $conn->prepare("UPDATE cbq
                SET cbq_active = 'T'
                WHERE :val BETWEEN cbq_minSupport AND cbq_maxSupport");
            if($pdo_statement->execute([':val'=>$tempVal])){
                $pdo_statement = $conn->prepare("UPDATE cbq
                SET cbq_active = 'F'
                WHERE :val  NOT BETWEEN cbq_minSupport AND cbq_maxSupport");
                //return 1 upon success, -1 otherwise
                if($pdo_statement->execute([':val'=>$tempVal]))
                    return 1;
                else
                    return -1;
            }
    }

    function getQueueLengthFromInstance($instance){
        if(!$instance instanceof LinkedList){
            return 0;
        }else{
            return $instance->getSize();
        }
    }

    function randomNumber($length) {
        $result = '';
    
        for($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }
    
        return $result;
    }

    function setClinicCapacity($conn, $cap){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }
        
        if(!is_int($cap)){
            if(is_float($cap) || is_double($cap))
                $cap = ceil($cap);
            else
                return -1;
        }

        $sql = "UPDATE clinic SET clinic_capacity=:newCap";
        $pdo_statement = $conn->prepare($sql);
        if($pdo_statement->execute([':newCap'=>$cap])){
            $status = refreshCBQPreset($conn);
            if($status == 1 || $status === 1)
                return 1;
            else
                return -1;
        }else{
            return -1;
        }
        
    }

    function setClinicLocation($conn, $clinicLatLng){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }
        $sql = "UPDATE clinic SET clinic_LatLng=:newLatLng";
        $pdo_statement = $conn->prepare($sql);
        if($pdo_statement->execute([':newLatLng'=>$clinicLatLng]))
            return 1;
        else
            return -1;
        
    }

    //check distance between user's latlng and the clinic's
    function checkDistance($conn, $userLatLng){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }
        $sql = "SELECT clinic_LatLng FROM clinic";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $result=$pdo_statement->fetch(PDO::FETCH_LAZY);
        $clinicLatLng = $result->clinic_LatLng;

        $earthRadius = 6371000; // Radius of the Earth in meters
    
        //clinic's LatLng
        [$lat1, $lon1] = array_map('trim', explode(',', $clinicLatLng));
        $lat1 = floatval($lat1);
        $lon1 = floatval($lon1);
    
        //user LatLng
        [$lat2, $lon2] = array_map('trim', explode(',', $userLatLng));
        $lat2 = floatval($lat2);
        $lon2 = floatval($lon2);
    
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
    
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
        $distance = $earthRadius * $c;

        $sql = "SELECT clinic_maxRadius FROM clinic";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $result=$pdo_statement->fetch(PDO::FETCH_LAZY);
        $maxRadius = (float) $result->clinic_maxRadius;
        //return round($distance, 2).", ".round($maxRadius, 2);
        if (round($distance, 5) <= round($maxRadius, 5)) {
            return true;
        } else {
            return false;
        }
    }
    
    
?>