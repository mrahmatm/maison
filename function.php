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

    function randomNumber($length) {
        $result = '';
    
        for($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }
    
        return $result;
    }




?>