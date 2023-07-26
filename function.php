<?php
    //include 'class.php';

    function deriveAgeFromIC($target){

        if(strlen($target) <= 0 || strlen($target) > 14)
            return null;

        if(strpos($target, "-") == FALSE)
            return null;
        
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
        if ($birthDate === false) { // Invalid date
            return null;
        }
        $nowDate = date_create(date('d-M-Y'));
        $dayDiff = date_diff($birthDate, $nowDate);

        return $dayDiff->y;
    }

    function deriveGenderFromIC($target){
        if(strlen($target) <= 0 || strlen($target) > 14)
            return null;

        if(strpos($target, "-") == FALSE)
            return null;
        
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

    function derivePersonnelID($conn, $dept_code, $dept_headCount){
        error_reporting(error_reporting() & ~E_DEPRECATED);
        $nowYear = date('Y');
        $strYear = strftime('Y', $nowYear);
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }
        $n = 1;
        $output = $dept_code."-".$nowYear."-".$n;
        while(true){
            $pdo_statement = $conn->prepare("SELECT * FROM personnel WHERE personnel_ID=:target");
            $pdo_statement->execute([':target'=>$output]);
            $result = $pdo_statement->fetch(PDO::FETCH_LAZY);
            if($result != NULL){
                $n++;
                $output = $dept_code."-".$nowYear."-".$n;
            }else{
                break;
            }
        }
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
                //echo "<script>alert('Only 1 node available')</script>";
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

    function traverseLinkedList($list, $q_ID=null) {
        $currentNode = $list->head;
        $count = 0;
        
        while ($currentNode !== null) {
            $queue = $currentNode->data;
            $count++;
            
            if($q_ID != null){
                if ($queue->q_ID === $q_ID) {
                    break; // Found the matching ID, break the loop
                }
            }
            
            $currentNode = $currentNode->after;
        }
        
        return $count;
    }

    function enqueue($conn, $queue, $holdQueueType = null){
        if(is_null($holdQueueType))
            $holdQueueType = $queue->q_type;

        if(checkQueueExists($conn, $holdQueueType) == 1){
            //$currentQueue = new LinkedList();
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }
            $queue = checkResetQueueID($conn, $queue);
            $currentQueue = readQueueInstance($conn, $holdQueueType);
            $currentTail = $currentQueue->getTail();
            $queue->setBefore($currentTail->data->q_ID);
            $currentQueue->insertAtTail($queue);
            //return $currentQueue;
            
            $sql = "DELETE FROM queue WHERE q_type =:targetValue";
            $pdo_statement = $conn->prepare($sql);
            $status = $pdo_statement->execute([':targetValue'=>$holdQueueType]);

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
                $pdo_statement->bindValue(':q_type', $holdQueueType);
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
        }else{
            return -1;
        }
    }

    //for late appointment patients only
    function insertLateCBQ($conn, $queue){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }
        //check if appointment exists
        $sql = "SELECT * FROM appointment WHERE q_ID=:targetID";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute([':targetID' => $queue->q_ID]);

        $sql = "DELETE FROM queue WHERE q_ID=:targetID";
        $pdo_statement = $conn->prepare($sql);
        if(!$pdo_statement->execute([':targetID'=>$queue->q_ID])){
            return -1;
        }

        if ($pdo_statement->rowCount() == 0) {
            return -1;
        } else {
            // Rows are fetched
            //check if GPQ exists, if it does not just enqueue normally
            if(checkQueueExists($conn, "GPQ") != 1){
                $queue->setType("GPQ");
                enqueue($conn, $queue);
                return 1;
            }
        }
        //if GPQ exists, use CBQ
        refreshCBQPreset($conn);
        //return var_dump($queue);
        $queue->setType("GPQ");
        

        $sql = "DELETE FROM appointment WHERE q_ID=:targetID";
        $pdo_statement = $conn->prepare($sql);
        if(!$pdo_statement->execute([':targetID'=>$queue->q_ID])){
            return -1;
        }

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
        $pdo_statement->execute([':targetPreset'=>$activePreset]);
        $result=$pdo_statement->fetch(PDO::FETCH_LAZY);
        $cbqX = $result->cbq_X;
        $cbqY = $result->cbq_Y;

        // Calculate the value of Y%
        $yValue = ($cbqY / 100) * $generalLength;

        // Calculate the Punishment Position using the derived value of Y%
        $punishmentPosition = ceil($cbqX + $yValue)-1;
        //echo "<script>alert('".$punishmentPosition."');</script>";
        if($GPQ->insertAfter($queue, $punishmentPosition)){
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
                //echo "<script>alert('Redundant q_id, renegerating...')</script>";
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
        //return $sum;

        //get number of present doctors
        $presentDr = countAllPresentDr($conn);

        //calculate current crowd score = sum all patients queueing / number of doctors
        $tempVal = $sum/$presentDr;
        //return $tempVal;
        //echo "<script>alert('Current score: ".$tempVal."')</script>";
        //set which CBQ preset to be used based on score

        $sql = "SELECT cbq_minSupport FROM cbq WHERE PRESET = 'LOW'";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $result=$pdo_statement->fetch(PDO::FETCH_LAZY);
        $lowMinSupp = $result->cbq_minSupport;

        $sql = "SELECT cbq_minSupport FROM cbq WHERE PRESET = 'MEDIUM'";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $result=$pdo_statement->fetch(PDO::FETCH_LAZY);
        $medMinSupp = $result->cbq_minSupport;

        $sql = "SELECT cbq_minSupport FROM cbq WHERE PRESET = 'HIGH'";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $result=$pdo_statement->fetch(PDO::FETCH_LAZY);
        $highMinSupp = $result->cbq_minSupport;
        //return $highMinSupp;
        if($tempVal >= $highMinSupp){
            $target = 'HIGH';
        }elseif($tempVal >= $medMinSupp){
            $target = 'MEDIUM';
        }else{
            $target = 'LOW';
        }

        $pdo_statement = $conn->prepare("UPDATE cbq
                SET cbq_active = 'T'
                WHERE PRESET = :target");
            if($pdo_statement->execute([':target'=>$target])){
                $pdo_statement = $conn->prepare("UPDATE cbq
                SET cbq_active = 'F'
                WHERE PRESET != :target");
                //return 1 upon success, -1 otherwise
                if($pdo_statement->execute([':target'=>$target]))
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

    function setClinicCapacity($conn, $cap, $cap1){
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

        if(!is_int($cap1)){
            if(is_float($cap1) || is_double($cap1))
                $cap1 = ceil($cap1);
            else
                return -1;
        }

        $sql = "UPDATE clinic SET clinic_capacity=:newCap, clinic_SLQMaxSize=:newCap1";
        $pdo_statement = $conn->prepare($sql);
        if($pdo_statement->execute([':newCap'=>$cap, ':newCap1'=>$cap1])){
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
        
        $sql = "SELECT clinic_LatLng, clinic_maxRadius FROM clinic";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $result=$pdo_statement->fetch(PDO::FETCH_OBJ);
        $latlng = $result->clinic_LatLng;
        
        $startIndex = strpos($latlng, "(") + 1;
        $endIndex = strpos($latlng, ")");
        $coordinates = explode(",", substr($latlng, $startIndex, $endIndex - $startIndex));
        $latitude = floatval(trim($coordinates[0]));
        $longitude = floatval(trim($coordinates[1]));

        $earthRadius = 6371000; // Radius of the Earth in meters
    
        //clinic's LatLng
        //[$lat1, $lon1] = array_map('trim', explode(',', $clinicLatLng));
        $lat1 = floatval($latitude);
        $lon1 = floatval($longitude);
    
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
            return "1";
        } else {
            return "0";
        }
    }
    
    function progressQueue($conn){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }
        
        $SLQExists = checkQueueExists($conn, "SLQ");
        $APQExists = checkQueueExists($conn, "APQ");
        $GPQExists = checkQueueExists($conn, "GPQ");

        if($SLQExists == 1 && $APQExists == 1 && $GPQExists == 1){
            //if all three queue exists
            $SLQState = readQueueInstance($conn, "SLQ");
            $SLQSize = $SLQState->getSize();

            $sql = "SELECT clinic_SLQMaxSize FROM clinic";
            $pdo_statement = $conn->prepare($sql);
            $pdo_statement->execute();
            $result=$pdo_statement->fetch(PDO::FETCH_LAZY);
            $SLQMaxSize = $result->clinic_SLQMaxSize;
            //max size allowed for SLQ vs current capacity
            $diff = $SLQMaxSize-$SLQSize;
            if($diff <= 0){
                //SLQ is full, return 0
                return 0;
            }else{
                //SLQ has space
                //dequeue from APQ, insert into SLQ
                $n = 0;
                while($n < $diff){
                    $current = dequeue($conn, "APQ");
                    $current->setType("SLQ");
                    enqueue($conn, $current);
                    $diff--;
                    $n++;
                }

                $n = 0;
                while($n < $diff){
                    $current = dequeue($conn, "GPQ");
                    $current->setType("SLQ");
                    enqueue($conn, $current);
                    //$diff--;
                    $n++;
                }
            }
            return 111;
        }elseif($SLQExists == 1 && $APQExists != 1 && $GPQExists == 1){
            //APQ takde, so fill SLQ dengan GPQ
            $sql = "SELECT clinic_SLQMaxSize FROM clinic";
            $pdo_statement = $conn->prepare($sql);
            $pdo_statement->execute();
            $result=$pdo_statement->fetch(PDO::FETCH_LAZY);
            $SLQMaxSize = $result->clinic_SLQMaxSize;

            $SLQState = readQueueInstance($conn, "SLQ");
            $SLQSize = $SLQState->getSize();

            $GPQState = readQueueInstance($conn, "GPQ");
            $GPQSize = $GPQState->getSize();
            $diff = $SLQMaxSize-$SLQSize;
            $n = 0;
            while($n < $diff && $n<$GPQSize){
                $current = dequeue($conn, "GPQ");
                $current->setType("SLQ");
                enqueue($conn, $current);
                $n++;
            }
            return 101;
        }elseif($SLQExists != 1 && $APQExists == 1 && $GPQExists == 1){
            //SLQ does not exist
            $sql = "SELECT clinic_SLQMaxSize FROM clinic";
            $pdo_statement = $conn->prepare($sql);
            $pdo_statement->execute();
            $result=$pdo_statement->fetch(PDO::FETCH_LAZY);
            $SLQMaxSize = $result->clinic_SLQMaxSize;

            $APQState = readQueueInstance($conn, "APQ");
            $APQSize = $APQState->getSize();
            $GPQState = readQueueInstance($conn, "APQ");
            $GPQSize = $GPQState->getSize();
            //masukkan APQ dulu
            if($APQSize >=  $SLQMaxSize){
                //cukup2 untuk APQ or APQ pun tak muat
                $n = 0;
                while($n < $SLQMaxSize){
                    $current = dequeue($conn, "APQ");
                    $current->setType("SLQ");
                    enqueue($conn, $current);
                    //$diff--;
                    $n++;
                }
            }else{
                //ada balance untuk GPQ
                //masukkan APQ dulu
                $n = 0;
                while($n < $SLQMaxSize && $n < $APQSize){
                    $current = dequeue($conn, "APQ");
                    $current->setType("SLQ");
                    enqueue($conn, $current);
                    $SLQMaxSize--;
                    $n++;
                }
                //then masukkan GPQ untuk balancing space
                $n = 0;
                while($n < $SLQMaxSize && $n < $GPQSize){
                    $current = dequeue($conn, "GPQ");
                    $current->setType("SLQ");
                    enqueue($conn, $current);
                    //$diff--;
                    $n++;
                }
            }

            return 11;
        }elseif($SLQExists != 1 && $APQExists != 1 && $GPQExists == 1){
            $sql = "SELECT clinic_SLQMaxSize FROM clinic";
            $pdo_statement = $conn->prepare($sql);
            $pdo_statement->execute();
            $result=$pdo_statement->fetch(PDO::FETCH_LAZY);
            $SLQMaxSize = $result->clinic_SLQMaxSize;

            $GPQState = readQueueInstance($conn, "GPQ");
            $GPQSize = $GPQState->getSize();

            $n = 0;
            while($n < $SLQMaxSize && $n < $GPQSize){
                $current = dequeue($conn, "GPQ");
                $current->setType("SLQ");
                enqueue($conn, $current);
                $n++;
            }

            return 1;
        }elseif($SLQExists != 1 && $APQExists == 1 && $GPQExists != 1){
            $sql = "SELECT clinic_SLQMaxSize FROM clinic";
            $pdo_statement = $conn->prepare($sql);
            $pdo_statement->execute();
            $result=$pdo_statement->fetch(PDO::FETCH_LAZY);
            $SLQMaxSize = $result->clinic_SLQMaxSize;

            $APQState = readQueueInstance($conn, "APQ");
            $APQSize = $APQState->getSize();

            $n = 0;
            while($n < $SLQMaxSize && $n < $APQSize){
                $current = dequeue($conn, "APQ");
                $current->setType("SLQ");
                enqueue($conn, $current);
                $n++;
            }

            return 1;
        }
    }

    function fetchAllPersonnel($conn){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }
        $sql = "SELECT * FROM personnel";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $result=$pdo_statement->fetchAll(PDO::FETCH_OBJ);
        if($result != null){
            //return $result;
            $fetchedPersonnels = array();
            foreach($result as $current){
                $create = new Personnel($conn, $current->personnel_ICNum, $current->personnel_name, $current->personnel_email, $current->personnel_phoneNum,  $current->personnel_type,  $current->dept_code,  $current->personnel_ID);
                $create->setAttend($current->personnel_attend);
                array_push($fetchedPersonnels,  $create);
            }
            return $fetchedPersonnels;
            //return $sql;
            }else{
                return 0;
                //return $sql;
            }
    }

    function fetchAllPatient($conn){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }
        $sql = "SELECT * FROM patient";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $result=$pdo_statement->fetchAll(PDO::FETCH_OBJ);
        if($result != null){
            //return $result;
            $fetchedPatients = array();
            foreach($result as $current){
                $create = new Patient($current->patient_ICNum, $current->patient_name, $current->patient_email, $current->patient_phoneNum);
                array_push($fetchedPatients,  $create);
            }
            return $fetchedPatients;
            //return $sql;
            }else{
                return 0;
                //return $sql;
            }
    }
    
    function fetchAllQueue($conn) {
        if ($conn == null) {
            $auth_type = "PER";
            require 'connect.php';
        }
    
        $SLQInstance = readQueueInstance($conn, "SLQ");
        $APQInstance = readQueueInstance($conn, "APQ");
        $GPQInstance = readQueueInstance($conn, "GPQ");
        /*
        $SLQJSON = $SLQInstance;
        if(!$SLQInstance instanceof LinkedList){
            $SLQJSON = 0;
        }

        $APQJSON = $APQInstance;
        if(!$APQInstance instanceof LinkedList){
            $APQJSON = 0;
        }

        $GPQJSON = $GPQInstance;
        if(!$GPQInstance instanceof LinkedList){
            $GPQJSON = 0;
        }*/
    
        $SLQJSON = $SLQInstance instanceof LinkedList ? $SLQInstance->toJSON() : 0;
        $APQJSON = $APQInstance instanceof LinkedList ? $APQInstance->toJSON() : 0;
        $GPQJSON = $GPQInstance instanceof LinkedList ? $GPQInstance->toJSON() : 0;
    
        return array($SLQJSON, $APQJSON, $GPQJSON);
    }

    function fetchAllDepartment($conn){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }
        $sql = "SELECT * FROM department";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $result=$pdo_statement->fetchAll(PDO::FETCH_OBJ);
        if($result != null){
            //return $result;
            $fetchedDepartments = array();
            foreach($result as $current){
                $create = new Department($current->dept_code, $current->dept_name, $current->dept_desc);
                $create->refreshDeptHeadCount($conn);
                array_push($fetchedDepartments,  $create);
            }
            return $fetchedDepartments;
            //return $sql;
            }else{
                return 0;
                //return $sql;
            }
    }

    function fetchAllService($conn){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }
        $sql = "SELECT * FROM service";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $result=$pdo_statement->fetchAll(PDO::FETCH_OBJ);
        if($result != null){
            //return $result;
            $fetchedServices = array();
            foreach($result as $current){
                $create = new Service($current->svc_code, $current->svc_desc, $current->svc_enable, $current->dept_code);
                //$create->refreshDeptHeadCount($conn);
                array_push($fetchedServices,  $create);
            }
            return $fetchedServices;
            //return $sql;
            }else{
                return null;
                //return $sql;
            }
    }

    function fetchAllAppointment($conn){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }

        $sql = "SELECT a.*, q.*, p.*
        FROM appointment a
        JOIN queue q ON a.q_ID = q.q_ID
        JOIN patient p ON q.patient_ICNum = p.patient_ICNum";

        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $result = $pdo_statement->fetchAll(PDO::FETCH_OBJ);

        if($result != null && $result !== null){
            return $result;
            }else{
                return null;
                //return $sql;
            }
    }

    function fetchCBQConfig($conn) {
        if ($conn == null) {
            $auth_type = "PER";
            require 'connect.php';
        }
    
        $sql = "SELECT * FROM cbq";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $result = $pdo_statement->fetchAll(PDO::FETCH_ASSOC);


        if ($result != null && count($result) > 0) {
            return json_encode($result);
        } else {
            return null;
        }
    }

    function fetchClinicConfig($conn){
        if ($conn == null) {
            $auth_type = "PER";
            require 'connect.php';
        }
    
        $sql = "SELECT * FROM clinic";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $result = $pdo_statement->fetch(PDO::FETCH_ASSOC);


        if ($result != null && count($result) > 0) {
            return json_encode($result);
        } else {
            return null;
        }
    }

    function insertAppQueue($conn, $queue, $datetime, $personnelID=null){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }
        $flag = 0;
        if($queue->q_type == "APP" || $queue->q_type === "APP"){
            $pdo_statement = $conn->prepare("INSERT INTO
                queue(q_ID, patient_ICNum, q_type, svc_code)
                VALUES (:q_ID, :patient_ICNum, :q_type, :svc_code)");
            if($pdo_statement->execute([
                ':q_ID'=>$queue->q_ID,
                ':patient_ICNum'=>$queue->patient_ICNum,
                ':q_type'=>$queue->q_type,
                ':svc_code'=>$queue->svc_code])){
                    $flag = 1;
                }

            if(is_null($personnelID)){
                $flag = insertAppointment($conn, $queue, $datetime);
            }else{
                $flag = insertAppointment($conn, $queue, $datetime, $personnelID);
            }
        }else{
            $flag = 0;
        }
        return $flag;
    }

    function compareDatetime($userDatetime, $appTime, $earlyTolerance, $lateTolerance){
        $userTimestamp = strtotime($userDatetime);
        $appTimestamp = strtotime($appTime);

        // Calculate the early and late datetime boundaries
        $earlyBoundary = $appTimestamp - ($earlyTolerance * 60);
        $lateBoundary = $appTimestamp + ($lateTolerance * 60);

        if ($userTimestamp < $earlyBoundary) {
            return -1; // User is too early
        } elseif ($userTimestamp >= $earlyBoundary && $userTimestamp <= $lateBoundary) {
            return 0; // User is within or exactly on time
        } elseif ($userTimestamp > $lateBoundary) {
            return 1; // User is late for the appointment
        }

        return -9; // Return -9 by default if the comparison fails
    }



    function insertAppointment($conn, $queue, $datetime, $personnelID=null){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }
        $flag = 0;
        if(is_null($personnelID)){
            $pdo_statement = $conn->prepare("INSERT INTO
                appointment(q_ID, app_datetime)
                VALUES (:q_ID, :app_datetime)");
            if($pdo_statement->execute([
                ':q_ID'=>$queue->q_ID,
                ':app_datetime'=>$datetime])){
                    $flag = 1;
                }
            
        }else{
            $pdo_statement = $conn->prepare("INSERT INTO
                appointment(q_ID, app_datetime, personnel_ID)
                VALUES (:q_ID, :app_datetime,  :personnel_ID)");
            if($pdo_statement->execute([
                ':q_ID'=>$queue->q_ID,
                ':app_datetime'=>$datetime,
                ':personnel_ID'=>$personnelID])){
                    $flag = 1;
                }
        }
        return $flag;
        
    }

    function convertToSqlDatetime($year, $month, $date, $hours, $minutes) {
        $datetime = sprintf("%04d-%02d-%02d %02d:%02d:00", $year, $month, $date, $hours, $minutes);
        return $datetime;
    }

    function APPtoAPQ($conn, $queueID){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }

        $sql = "SELECT * FROM appointment WHERE q_ID=:targetID";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute([':targetID'=>$queueID]);
        $resultApp=$pdo_statement->fetch(PDO::FETCH_OBJ);

        //return $resultApp;
        
        $sql = "SELECT * FROM queue WHERE q_ID=:targetID";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute([':targetID'=>$resultApp->q_ID]);
        $resultQueue=$pdo_statement->fetch(PDO::FETCH_OBJ);

        //return $resultQueue;

        $holdID = $resultQueue->q_ID;
        $create = new Queue($resultQueue->q_type, $resultQueue->patient_ICNum, $resultQueue->svc_code);
        $create->setID($holdID);
        $create->setType("APQ");

        //return $create;
        
        $sql = "DELETE FROM appointment WHERE q_ID=:targetID";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute([':targetID'=>$create->q_ID]);
        $sql = "DELETE FROM queue WHERE q_ID=:targetID";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute([':targetID'=>$create->q_ID]);

        enqueue($conn, $create);
    }

    function searchPatientByEmail($conn, $email){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }
        $sql = "SELECT * FROM patient WHERE patient_email=:targetEmail";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute([':targetEmail'=>$email]);
        $result=$pdo_statement->fetch(PDO::FETCH_OBJ);
        if($result != null && $result !== null){
            return 1;
        }else{
            return 0;
        }
    }

    function searchPatientByICNum($conn, $ic){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }
        $sql = "SELECT * FROM patient WHERE patient_ICNum=:targetIC";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute([':targetIC'=>$ic]);
        $result=$pdo_statement->fetch(PDO::FETCH_OBJ);
        if($result != null && $result !== null){
            return 1;
        }else{
            return 0;
        }
    }

    function fetchPatientByEmail($conn, $email){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }
        $sql = "SELECT * FROM patient WHERE patient_email=:targetEmail";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute([':targetEmail'=>$email]);
        $result=$pdo_statement->fetch(PDO::FETCH_OBJ);
        if($result != null && $result !== null){
            return $result;
        }else{
            return 0;
        }
    }

    function searchAppointmentByEmail($conn, $email){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }
        $sql = "SELECT * FROM patient WHERE patient_email=:targetEmail";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute([':targetEmail'=>$email]);
        $result=$pdo_statement->fetch(PDO::FETCH_OBJ);

        $targetIC=$result->patient_ICNum;

        $sql = "SELECT * FROM queue WHERE patient_ICNum=:target AND q_type='APP'";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute([':target'=>$targetIC]);
        $result=$pdo_statement->fetchAll(PDO::FETCH_OBJ);
        if($result != null && $result !== null){
            $fetchAppointments = array();
            foreach($result as $appointment){
                $sql = "SELECT * FROM appointment WHERE q_ID=:target";
                $pdo_statement = $conn->prepare($sql);
                $pdo_statement->execute([':target'=>$appointment->q_ID]);
                $resultApp=$pdo_statement->fetch(PDO::FETCH_OBJ);
                //echo $appointment
                $sql = "SELECT * FROM service WHERE svc_code=:target";
                $pdo_statement = $conn->prepare($sql);
                $pdo_statement->execute([':target'=>$appointment->svc_code]);
                $resultSvc=$pdo_statement->fetch(PDO::FETCH_OBJ);
                if(is_bool($resultSvc)){
                    $create = new AppointmentQueue($resultApp->app_datetime, "NaN");
                }else{
                    $create = new AppointmentQueue($resultApp->app_datetime, $resultSvc->svc_desc);
                }
                
                array_push($fetchAppointments, $create);
            }
            return json_encode($fetchAppointments);
        }else{
            return 0;
        }
    }

    function personnelLogin($conn, $id, $IC){
        $sql = "SELECT * FROM personnel WHERE personnel_ID=:targetID AND personnel_ICNum=:targetIC";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute([':targetID'=>$id, ':targetIC'=>$IC]);
        $result=$pdo_statement->fetch(PDO::FETCH_OBJ);
        if($result != null && $result !== null){
            return json_encode($result);
        }else{
            return 0;
        }
    }

    function fetchAllServiceInfo($conn){
        $sql = "SELECT * FROM service WHERE svc_enable=1";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $result=$pdo_statement->fetchAll(PDO::FETCH_OBJ);
        if($result != null && $result !== null){
            $fetchedServices = array();
            foreach($result as $service){
                $create = new Service($service->svc_code, $service->svc_desc, $service->svc_enable);
                array_push($fetchedServices, $create);
            }
            return json_encode($fetchedServices);
        }else{
            return 0;
        }
    }

    function insertNewPatientAppointment($conn, $email, $selectService, $datetime){

        // Convert the string to a DateTime object with the specified time zone
        $date = new DateTime($datetime, new DateTimeZone('Asia/Kuala_Lumpur'));

        // Get the current date and time in the same time zone
        $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
        $currentDate = new DateTime($currentDateTime->format('Y-m-d'), new DateTimeZone('Asia/Kuala_Lumpur'));

        // Add one day to the current date to calculate "tomorrow" in the same time zone
        $tomorrow = $currentDate->modify('+1 day');

        // Check if the date is tomorrow or onwards
        if ($date >= $tomorrow) {

            // Check if the time is between 8am and 4pm
            $time = $date->format('H:i');
            if ($time >= '08:00' && $time <= '16:00') {
                //return "1"; // Both conditions satisfied
                //so proceed
            } else {
                return "-1"; // Time condition not satisfied
            }

        } else {
            return "-2"; // Date condition not satisfied
        }

        // Get the appointment time in the correct format (Y-m-d H:i:s)
        $appointmentTime = $date->format('Y-m-d H:i:s');
        
        $sql = "SELECT * FROM clinic";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $result = $pdo_statement->fetch(PDO::FETCH_OBJ);
        //$result = json_decode($result);
        //return (var_dump($result));
        $allowedInterval = $result->clinic_appointmentInterval;
        $startString = "-".$allowedInterval." minutes";
        $endString = "+".($allowedInterval*2)." minutes";

        // Calculate the time range (15 minutes before and after the given time)
        $startRange = $date->modify($startString)->format('Y-m-d H:i:s');
        $endRange = $date->modify($endString)->format('Y-m-d H:i:s');

        // Reset the date object back to the original appointment time
        $date->modify($startString);

        // Prepare and execute the SQL query to check for existing appointments within the range
        $sql = "SELECT COUNT(*) as count FROM appointment WHERE app_datetime BETWEEN :startRange AND :endRange OR app_datetime = :appointmentTime";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute([
            ":startRange" => $startRange,
            ":endRange" => $endRange,
            ":appointmentTime" => $appointmentTime
        ]);

        // Fetch and return the result
        $result = $pdo_statement->fetch(PDO::FETCH_ASSOC);
        $count = $result['count'];

        if($count > 0){
            return "-3"; //overlapping appointment, 15 minutes threshold
        }


        $sql = "SELECT * FROM service WHERE svc_desc=:targetDesc";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute([":targetDesc"=>$selectService]);
        $fetchedService=$pdo_statement->fetch(PDO::FETCH_OBJ);

        $sql = "SELECT * FROM patient WHERE patient_email=:targetEmail";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute([":targetEmail"=>$email]);
        $fetchedPatient=$pdo_statement->fetch(PDO::FETCH_OBJ);

        $create = new Queue("APP", $fetchedPatient->patient_ICNum, $fetchedService->svc_code);
        $create = checkResetQueueID($conn, $create);

        $sql = "INSERT INTO queue(q_ID, q_type, patient_ICNum, svc_code)
            VALUES (:q_ID, :q_type, :patient_ICNum, :svc_code)";
        $pdo_statement = $conn->prepare($sql);
        if($pdo_statement->execute([":q_ID"=>$create->q_ID, ":q_type"=>$create->q_type,
        ":patient_ICNum"=>$create->patient_ICNum, ":svc_code"=>$create->svc_code])){
            //if insert queue success
            $sql = "INSERT INTO appointment(q_ID, app_datetime)
                VALUES (:q_ID, :app_datetime)";
            $pdo_statement = $conn->prepare($sql);
            if($pdo_statement->execute([":q_ID"=>$create->q_ID, ":app_datetime"=>$datetime])){
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }

    function convertToSQLDateTimeHTML($inputDate, $inputTime) {
        // Combine date and time values into a single string
        $combinedDateTime = $inputDate . ' ' . $inputTime;
      
        // Create a DateTime object from the combined date and time
        $dateTime = new DateTime($combinedDateTime);
      
        // Format the DateTime object into SQL-formatted datetime
        $sqlDateTime = $dateTime->format('Y-m-d H:i:s');
      
        return $sqlDateTime;
    }

    function checkQueueByIC($conn, $ic){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }
        $sql = "SELECT * FROM queue WHERE patient_ICNum=:target AND q_type<>'APP'";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute([':target'=>$ic]);
        $result=$pdo_statement->fetch(PDO::FETCH_OBJ);
        if($result != null && $result !== null){
            return $result;
        }else{
            return 0;
        }
    }





?>