<?php
    include 'function.php';
    class Patient{
        public $patient_ICNum;
        public $patient_name;
        public $patient_gender;
        public $patient_age;
        public $patient_email;
        public $patient_phoneNum;

        function __construct($ICNum, $name, $email, $phoneNum){
            $this->patient_ICNum = $ICNum;
            $this->patient_name = $name;
            $this->patient_gender = deriveGenderFromIC($ICNum);
            $this->patient_age = deriveAgeFromIC($ICNum);
            $this->patient_email = $email;
            $this->patient_phoneNum = $phoneNum;
        }

        function checkICNum($conn){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }

            $pdo_statement = $conn->prepare("SELECT * FROM patient WHERE patient_ICNum=:target");
            $pdo_statement->execute([':target'=>$this->patient_ICNum]);
            $result = $pdo_statement->fetch();

            if($result >  0){
                return 1;
            }else{
                return 0;
            }
        }

        function checkEmail($conn){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }

            $pdo_statement = $conn->prepare("SELECT * FROM patient WHERE patient_email=:target");
            $pdo_statement->execute([':target'=>$this->patient_email]);
            $result = $pdo_statement->fetch();

            if($result >  0){
                return 1;
            }else{
                return 0;
            }
        }

        function addPatient($conn){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }
            if($this->checkICNum($conn) == 0 && $this->checkEmail($conn) == 0){
                $pdo_statement = $conn->prepare("INSERT INTO
                    patient(patient_ICNum, patient_name, patient_gender, patient_age, patient_email, patient_phoneNum)
                    VALUES (:patient_ICNum, :patient_name, :patient_gender, :patient_age, :patient_email, :patient_phoneNum)");
            if($pdo_statement->execute([
                ':patient_ICNum'=>$this->patient_ICNum,
                ':patient_name'=>$this->patient_name,
                ':patient_gender'=>$this->patient_gender,
                ':patient_age'=>$this->patient_age,
                ':patient_email'=>$this->patient_email,
                ':patient_phoneNum'=>$this->patient_phoneNum]))
                return 1;
            else
                return -1;
            }else{
                return 0;
            }
        }

        function deletePatient($conn, $targetPatient){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }

            if($targetPatient->checkICNum($conn) == 1){
                $pdo_statement = $conn->prepare("DELETE FROM patient WHERE patient_ICNum=:patient_ICNum");
                if($pdo_statement->execute([':patient_ICNum'=>$targetPatient->patient_ICNum])){
                    return 1;
                }else{
                    return 0;
                }
            }else{
                return -1;
            }
        }

        function fetchPatient($conn, $targetValue, $targetField){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }
            $sql = "SELECT * FROM patient WHERE ".$targetField." = :targetValue";
            $pdo_statement = $conn->prepare($sql);
            $pdo_statement->execute([':targetValue'=>$targetValue]);
            $result=$pdo_statement->fetch(PDO::FETCH_LAZY);
            if($result != null){
                    $fetchedPatient = new Patient($result->patient_ICNum, $result->patient_name, $result->patient_gender, $result->patient_age, $result->patient_email, $result->patient_phoneNum);
                    return $fetchedPatient;
                    //return $sql;
                }else{
                    return 0;
                    //return $sql;
                }
        }

        function updatePatient($conn, $targetPatient, $updatedPatient){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }

            if($targetPatient->checkICNum($conn) == 1){
                $pdo_statement = $conn->prepare("UPDATE patient
                    SET patient_ICNum=:patient_ICNum, patient_name=:patient_name, patient_email=:patient_email, patient_phoneNum=:patient_phoneNum
                    WHERE patient_ICNum=:targetICNum");
                if($pdo_statement->execute([':patient_ICNum'=>$updatedPatient->patient_ICNum, ':patient_name'=>$updatedPatient->patient_name, ':patient_email'=>$updatedPatient->patient_email, ':patient_phoneNum'=>$updatedPatient->patient_phoneNum, ':targetICNum'=>$targetPatient->patient_ICNum])){
                    $pdo_statement = $conn->prepare("UPDATE patient
                    SET patient_age=:patient_age, patient_gender=:patient_gender
                    WHERE patient_ICNum=:targetICNum");
                    $newAge = deriveAgeFromIC($updatedPatient->patient_ICNum);
                    $newGender = deriveGenderFromIC($updatedPatient->patient_ICNum);
                    if($pdo_statement->execute([':patient_age'=>$newAge, ':patient_gender'=>$newGender, ':targetICNum'=>$updatedPatient->patient_ICNum]))
                        return 1;
                    else
                        return -1;
                }else{
                    return -1;
                }
            }else{
                return -1;
            }
        }
    }

    class Personnel{
        public $personnel_ID;
        public $personnel_ICNum;
        public $personnel_name;
        public $personnel_gender;
        public $personnel_age;
        public $personnel_email;
        public $personnel_phoneNum;
        public $personnel_type;
        public $dept_code;
        public $personnel_attend;

        function __construct($conn, $ICNum, $name, $email, $phoneNum, $type, $dept_code, $personnel_ID="NULL"){
            $this->personnel_ICNum = $ICNum;
            $this->personnel_name = $name;
            $this->personnel_gender = deriveGenderFromIC($ICNum);
            $this->personnel_age = deriveAgeFromIC($ICNum);
            $this->personnel_email = $email;
            $this->personnel_phoneNum = $phoneNum;
            $this->personnel_type = $type;
            $this->dept_code = $dept_code;

            if(strcasecmp("NULL", $personnel_ID) == 0){
                if($conn == null){
                    $auth_type = "PER";
                    require 'connect.php';
                }
    
                $tempDept = new Department ($dept_code, "temp", "temp");
                $tempVar = $tempDept->refreshDeptHeadCount($conn);
                $tempHeadCount = $tempDept->getDeptHeadCount($conn);
                $this->personnel_ID = derivePersonnelID($conn, $dept_code, $tempHeadCount);
            }else{
                $this->personnel_ID = $personnel_ID;
            }
        }

        function setAttend($attend){
            $this->personnel_attend = $attend;
        }

        function addPersonnel($conn, $newPersonnel){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }
            if($newPersonnel->checkICNum($conn) == 1){
                return -2;
            }
                $pdo_statement = $conn->prepare("INSERT INTO
                    personnel(personnel_ID, personnel_ICNum, personnel_name, personnel_gender, personnel_age, personnel_email, personnel_phoneNum, personnel_type, dept_code)
                    VALUES (:personnel_ID, :personnel_ICNum, :personnel_name, :personnel_gender, :personnel_age, :personnel_email, :personnel_phoneNum, :personnel_type, :dept_code)");
            if($pdo_statement->execute([
                ':personnel_ID'=>$newPersonnel->personnel_ID,
                ':personnel_ICNum'=>$newPersonnel->personnel_ICNum,
                ':personnel_name'=>$newPersonnel->personnel_name,
                ':personnel_gender'=>$newPersonnel->personnel_gender,
                ':personnel_age'=>$newPersonnel->personnel_age,
                ':personnel_email'=>$newPersonnel->personnel_email,
                ':personnel_phoneNum'=>$newPersonnel->personnel_phoneNum,
                ':personnel_type'=>$newPersonnel->personnel_type,
                ':dept_code'=>$newPersonnel->dept_code]))
                return 1;
            else
                return 0;
        }

        function fetchPersonnel($conn, $targetValue, $targetField){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }
            $sql = "SELECT * FROM personnel WHERE ".$targetField." = :targetValue";
            $pdo_statement = $conn->prepare($sql);
            $pdo_statement->execute([':targetValue'=>$targetValue]);
            $result=$pdo_statement->fetch(PDO::FETCH_LAZY);
            if($result != null){
                    $fetchedPersonnel = new Personnel($conn, $result->personnel_ICNum, $result->personnel_name, $result->personnel_email, $result->personnel_phoneNum,  $result->personnel_type,  $result->dept_code,  $result->personnel_ID);
                    return $fetchedPersonnel;
                    //return $sql;
                }else{
                    return 0;
                    //return $sql;
                }
        }

        function updatePersonnel($conn, $targetPersonnel, $updatedPersonnel){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }

            $pdo_statement = $conn->prepare("UPDATE personnel
                SET personnel_ICNum=:personnel_ICNum, personnel_name=:personnel_name, personnel_email=:personnel_email, personnel_phoneNum=:personnel_phoneNum, personnel_type=:personnel_type, dept_code=:dept_code, personnel_ID=:personnel_ID
                WHERE personnel_ID=:targetID");
            if($pdo_statement->execute([':personnel_ICNum'=>$updatedPersonnel->personnel_ICNum, ':personnel_name'=>$updatedPersonnel->personnel_name,
                ':personnel_email'=>$updatedPersonnel->personnel_email, ':personnel_phoneNum'=>$updatedPersonnel->personnel_phoneNum,
                ':personnel_type'=>$updatedPersonnel->personnel_type, ':dept_code'=>$updatedPersonnel->dept_code,
                ':personnel_ID'=>$updatedPersonnel->personnel_ID, ':targetID'=>$targetPersonnel->personnel_ID])){
                $pdo_statement = $conn->prepare("UPDATE personnel
                SET personnel_age=:personnel_age, personnel_gender=:personnel_gender
                WHERE personnel_ID=:targetID");
                $newAge = deriveAgeFromIC($updatedPersonnel->personnel_ICNum);
                $newGender = deriveGenderFromIC($updatedPersonnel->personnel_ICNum);
                if($pdo_statement->execute([':personnel_age'=>$newAge, ':personnel_gender'=>$newGender, ':targetID'=>$updatedPersonnel->personnel_ID]))
                    return 1;
                else
                    return -1;
            }else{
                return -1;
            }

        }

        function deletePersonnel($conn, $targetPersonnel){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }

            $pdo_statement = $conn->prepare("DELETE FROM personnel WHERE personnel_ID=:personnel_ID");
            if($pdo_statement->execute([':personnel_ID'=>$targetPersonnel->personnel_ID])){
                return 1;
            }else{
                return 0;
            }

        }

        function checkICNum($conn){
                if($conn == null){
                    $auth_type = "PER";
                    require 'connect.php';
                }
    
                $pdo_statement = $conn->prepare("SELECT * FROM personnel WHERE personnel_ICNum=:target");
                $pdo_statement->execute([':target'=>$this->personnel_ICNum]);
                $result = $pdo_statement->fetch();
    
                if($result >  0){
                    return 1;
                }else{
                    return 0;
                }
        }
    }

    class Department{
        public $dept_code;
        public $dept_name;
        public $dept_desc;
        public $dept_headCount;

        function __construct($code, $name, $desc){
            $this->dept_code = $code;
            $this->dept_name = $name;
            $this->dept_desc = $desc;
        }

        function checkDeptCode($conn){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }
            $targetDept = $this->dept_code;
            $pdo_statement = $conn->prepare("SELECT * FROM department WHERE dept_code=:targetCode");
            $pdo_statement->execute([':targetCode'=>$targetDept]);
            $result = $pdo_statement->fetch();

            if($result > 0){
                return 1;
            }else{
                return 0;
            }
        }
        
        function addDepartment($conn, $newDept){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }

            if($newDept->checkDeptCode($conn) == 0){
                $pdo_statement = $conn->prepare("INSERT INTO
                    department(dept_code, dept_name, dept_desc, dept_headCount)
                    VALUES (:dept_code, :dept_name, :dept_desc, :dept_headCount)");
            if($pdo_statement->execute([
                ':dept_code'=>$newDept->dept_code,
                ':dept_name'=>$newDept->dept_name,
                ':dept_desc'=>$newDept->dept_desc,
                ':dept_headCount'=>0])){
                    $newDept->refreshDeptHeadCount($conn);
                    return 1;
                }
            else
                return -1;
            }else{
                return 0;
            }

        }

        function refreshDeptHeadCount($conn){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }

            $sql = "SELECT * FROM personnel WHERE dept_code = :targetValue";
            $pdo_statement = $conn->prepare($sql);
            $pdo_statement->execute([':targetValue'=>$this->dept_code]);
            $result=$pdo_statement->fetchAll();
            $count = sizeof($result);
            if($count > 0){
                    //$fetchedPatient = new Patient($result->patient_ICNum, $result->patient_name, $result->patient_gender, $result->patient_age, $result->patient_email, $result->patient_phoneNum);
                    //return $result->fetchColumn();

                    $sql = "UPDATE department SET dept_headCount=:newCount WHERE dept_code = :targetValue";
                    $pdo_statement = $conn->prepare($sql);
                    $pdo_statement->execute([':newCount'=>$count, ':targetValue'=>$this->dept_code]);
                    $this->dept_headCount = $count;
                    //return $count;
                }else{
                    $sql = "UPDATE department SET dept_headCount=:newCount WHERE dept_code = :targetValue";
                    $pdo_statement = $conn->prepare($sql);
                    $pdo_statement->execute([':newCount'=>0, ':targetValue'=>$this->dept_code]);
                    $this->dept_headCount = 0;
                    //return 0;
                }
        }

        function getDeptHeadCount($conn){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }
            $this->refreshDeptHeadCount($conn);
            $sql = "SELECT dept_headCount FROM department WHERE dept_code = :targetValue";
            $pdo_statement = $conn->prepare($sql);
            $pdo_statement->execute([':targetValue'=>$this->dept_code]);
            $result=$pdo_statement->fetch(PDO::FETCH_LAZY);
            if($result != null){
                    //$fetchedPatient = new Patient($result->patient_ICNum, $result->patient_name, $result->patient_gender, $result->patient_age, $result->patient_email, $result->patient_phoneNum);
                    return $result->dept_headCount;
                    //return $sql;
                }else{
                    return -1;
                    //return $sql;
                }
        }

        function updateDepartment($conn, $targetDept, $updatedDept){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }

            if($targetDept->checkDeptCode($conn) == 1){
                $pdo_statement = $conn->prepare("UPDATE department
                    SET dept_code=:dept_code, dept_name=:dept_name, dept_desc=:dept_desc
                    WHERE dept_code=:targetDeptCode");
                if($pdo_statement->execute([':dept_code'=>$updatedDept->dept_code, ':dept_name'=>$updatedDept->dept_name, 
                    ':dept_desc'=>$updatedDept->dept_desc,':targetDeptCode'=>$targetDept->dept_code])){
                        $updatedDept->refreshDeptHeadCount($conn);
                        return 1;
                }else{
                    return -1;
                }
            }else{
                return -1;
            }
        }

        function fetchDepartment($conn, $targetValue, $targetField){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }
            $sql = "SELECT * FROM department WHERE ".$targetField." = :targetValue";
            $pdo_statement = $conn->prepare($sql);
            $pdo_statement->execute([':targetValue'=>$targetValue]);
            $result=$pdo_statement->fetch(PDO::FETCH_LAZY);
            if($result != null){
                    $fetchedDepartment = new Department($result->dept_code, $result->dept_name, $result->dept_desc, $result->dept_desc);
                    return $fetchedDepartment;
                    //return $sql;
                }else{
                    return 0;
                    //return $sql;
                }
        }

        function deleteDepartment($conn, $targetDept){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }

            if($targetDept->checkDeptCode($conn) == 1){
                $pdo_statement = $conn->prepare("DELETE FROM department WHERE dept_code=:dept_code");
                if($pdo_statement->execute([':dept_code'=>$targetDept->dept_code])){
                    $pdo_statement = $conn->prepare("UPDATE personnel SET dept_code = 'NA' WHERE dept_code=:dept_code");
                    if($pdo_statement->execute([':dept_code'=>$targetDept->dept_code])){
                        $tempDept = new Department("NA", "temp", "temp");
                        $tempCount = $tempDept->refreshDeptHeadCount($conn);
                        return 1;
                    }else
                        return 0;
                }else{
                    return 0;
                }
            }else{
                return -1;
            }
        }


    }

    class Operation{
        public $personnel_ID;
        public $op_position;
        public $op_area;

        function __construct($personnel_ID, $op_position, $op_area){
            $this->personnel_ID = $personnel_ID;
            $this->op_position = $op_position;
            $this->op_area = $op_area;
        }

        function checkPersonnelID($conn){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }

            $pdo_statement = $conn->prepare("SELECT * FROM operation WHERE personnel_ID=:target");
                $pdo_statement->execute([':target'=>$this->personnel_ID]);
                $result = $pdo_statement->fetch();
    
                if($result > 0){
                    return 1;
                }else{
                    return 0;
                }
        }

        function addOperation($conn, $targetPersonnel){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }
            $tempOperation = new Operation($targetPersonnel->personnel_ID, "temp", "temp");
            if($tempOperation->checkPersonnelID($conn) == 0){
                $pdo_statement = $conn->prepare("INSERT INTO
                    operation(personnel_ID, op_position, op_area)
                    VALUES (:personnel_ID, :op_position, :op_area)");
            if($pdo_statement->execute([
                ':personnel_ID'=>$targetPersonnel->personnel_ID,
                ':op_position'=>$this->op_position,
                ':op_area'=>$this->op_area]))
                return 1;
            }else{
                return 0;
            }
        }
    }

    class Queue{
        public $q_ID;
        public $q_before;
        public $q_after;
        public $q_type;
        public $patient_ICNum;
        public $svc_code;

        function __construct($type, $patient_ICNum, $svc_code = null) {
            $this->q_ID = randomNumber(3);
            $this->q_type = $type;
            $this->patient_ICNum = $patient_ICNum;
            $this->svc_code = $svc_code;
        }

        function setBefore($beforeID){
            $this->q_before = $beforeID;
        }

        function setAfter($afterID){
            $this->q_after = $afterID;
        }

        function resetID(){
            $this->q_ID = randomNumber(3);
        }

        function setID($id){
            $this->q_ID = $id;
        }

        function setType($type){
            $this->q_type = $type;
        }

        public function toString() {
            $output = "ID: " . $this->q_ID . "<br>";
            $output .= "Type: " . ($this->q_type ?? "NULL") . "<br>";
            $output .= "Patient ICNum: " . ($this->patient_ICNum ?? "NULL") . "<br>";
            $output .= "Service Code: " . ($this->svc_code ?? "NULL") . "<br>";
            $output .= "Before: " . ($this->q_before ?? "NULL") . "<br>";
            $output .= "After: " . ($this->q_after ?? "NULL") . "<br>";
            $output .= "--------------<br>";
        
            return $output;
        }
        

    }
    
    class Node{
        public $before;
        public $after;
        public $data;

        function __construct($queue){
            $this->before = $queue->q_before;
            $this->after = $queue->q_after;
            $this->data = $queue;
        }

        public function toString() {
            $output = "Node:\n";
            $output .= "Before: " . $this->before . "\n";
            $output .= "After: " . $this->after . "\n";
            $output .= "Data:\n";
    
            $queue = $this->data;
            $output .= "ID: " . $queue->q_ID . "\n";
            $output .= "Type: " . $queue->q_type . "\n";
            $output .= "Patient ICNum: " . $queue->patient_ICNum . "\n";
            $output .= "Service Code: " . $queue->svc_code . "\n";
    
            return $output;
        }
    }

    class LinkedList {
        public $head;
        public  $tail;
    
        public function __construct() {
            $this->head = null;
            $this->tail = null;
        }
    
        public function insertAtHead($queue) {
            $newNode = new Node($queue);
            $newNode->after = $this->head;
            if ($this->head !== null) {
                $this->head->before = $newNode;
            }
            $this->head = $newNode;
            if ($this->tail === null) {
                $this->tail = $newNode;
            }
        }
        
        public function insertAtTail($queue) {
            $newNode = new Node($queue);
        
            if ($this->tail === null) {
                $this->head = $newNode;
                $this->tail = $newNode;
            } else {
                $newNode->before = $this->tail;
                $newNode->after = null; // Set the "after" value of the new node to null since it's being inserted at the tail
        
                // Update the "after" value of the previous tail to the address of the new tail
                $this->tail->data->setAfter($newNode->data->q_ID); // Updated line
        
                $this->tail->after = $newNode; // Updated line
                $this->tail = $newNode;
            }
        }

        public function removeHead() {
            if ($this->head === null) {
                return null; // Empty list, nothing to remove
            }
        
            $removedNode = $this->head;
        
            if ($this->head === $this->tail) {
                // Only one node in the list
                $this->head = null;
                $this->tail = null;
            } else {
                $this->head = $this->head->after;
                $this->head->data->setBefore(null); // Reset the "before" value of the new head to null
            }
        
            return $removedNode->data; // Return the data of the removed node if needed
        }
        
        public function insertAfter($newQueue, $position) {
            $newNode = new Node($newQueue);
        
            if ($position <= 0) {
                // Insert at the head
                $newNode->after = $this->head;
                if ($this->head !== null) {
                    $this->head->before = $newNode;
                }
                $this->head = $newNode;
                if ($this->tail === null) {
                    $this->tail = $newNode;
                }
            } else {
                $currentNode = $this->head;
                $count = 0;
        
                while ($currentNode !== null && $count < $position) {
                    $currentNode = $currentNode->after;
                    $count++;
                }
        
                if ($currentNode !== null) {
                    $newNode->after = $currentNode->after;
                    $newNode->before = $currentNode;
        
                    if ($currentNode->after !== null) {
                        $currentNode->after->before = $newNode;
                    } else {
                        $this->tail = $newNode;
                    }
        
                    $currentNode->after = $newNode;
                } else {
                    // Position is greater than the list size, insert at the tail
                    $newNode->before = $this->tail;
                    $this->tail->after = $newNode;
                    $this->tail = $newNode;
                }
            }
        
            if ($newNode->before !== null) {
                $newNode->data->setBefore($newNode->before->data->q_ID); // Set the "before" value of the new node
                $newNode->before->data->setAfter($newNode->data->q_ID); // Set the "after" value of the previous node
            }
            if ($newNode->after !== null) {
                $newNode->data->setAfter($newNode->after->data->q_ID); // Set the "after" value of the new node
                $newNode->after->data->setBefore($newNode->data->q_ID); // Set the "before" value of the next node
            }
        
            return $newNode !== null ? true : -1;
        }
    
        public function getSize() {
            $count = 0;
            $currentNode = $this->head;
            while ($currentNode !== null) {
                $count++;
                $currentNode = $currentNode->after;
            }
            return $count;
        }

        public function getTail(){
            return $this->tail;
        }

        public function getNodeById($id) {
            $currentNode = $this->head;
            while ($currentNode !== null) {
                if ($currentNode->id === $id) {
                    return $currentNode;
                }
                $currentNode = $currentNode->after;
            }
            return null; // Node not found
        }
        
        private function findNodeByID($nodeID) {
            $current = $this->head;
    
            while ($current !== null) {
                $queue = $current->data;
    
                if ($queue->q_ID === $nodeID) {
                    return $current;
                }
    
                $current = $current->next;
            }
    
            return null; // Node not found
        }

        public function displayForward() {
            $currentNode = $this->head;
            while ($currentNode !== null) {
                echo @$currentNode->id . " ";
                @$currentNode = $currentNode->after;
            }
            echo "<br>";
        }

        public function displayAllForward() {
            $current = $this->head;
    
            while ($current !== null) {
                $queue = $current->data;
                
                echo "ID: " . $queue->q_ID . "<br>";
                echo "Type: " . $queue->q_type . "<br>";
                echo "Patient ICNum: " . $queue->patient_ICNum . "<br>";
                echo "Service Code: " . $queue->svc_code . "<br>";
                echo "Before: " . $queue->q_before . "<br>";
                echo "After: " . $queue->q_after . "<br>";
                echo "--------------<br>";
                
                $current = $current->after;
            }
        }

        public function toString() {
            $current = $this->head;
            $output = '';
    
            while ($current !== null) {
                $queue = $current->data;
    
                $output .= "ID: " . $queue->q_ID . "\n";
                $output .= "Type: " . $queue->q_type . "\n";
                $output .= "Patient ICNum: " . $queue->patient_ICNum . "\n";
                $output .= "Service Code: " . $queue->svc_code . "\n";
                $output .= "Before: " . $queue->q_before . "\n";
                $output .= "After: " . $queue->q_after . "\n";
                $output .= "--------------\n";
    
                $current = $current->after;
            }
    
            return $output;
        }

        public function toJSON() {
            $current = $this->head;
            $data = [];
    
            while ($current !== null) {
                $queue = $current->data;
    
                $queueData = [
                    'q_ID' => $queue->q_ID,
                    'q_type' => $queue->q_type,
                    'patient_ICNum' => $queue->patient_ICNum,
                    'svc_code' => $queue->svc_code,
                    'q_before' => $queue->q_before,
                    'q_after' => $queue->q_after
                ];
    
                $data[] = $queueData;
    
                $current = $current->after;
            }
    
            return json_encode($data);
        }
    }
    
    class Service{
        public $svc_code;
        public $svc_desc;
        public $svc_enable;
        public $dept_code;

        function __construct($code, $desc, $enable, $dept_code="NULL"){
            $this->svc_code = $code;
            $this->svc_desc = $desc;
            $this->svc_enable = $enable;
            if($dept_code != "NULL")
                $this->dept_code = $dept_code;
        }

        function checkSvcCode($conn){
                if($conn == null){
                    $auth_type = "PER";
                    require 'connect.php';
                }
    
                $pdo_statement = $conn->prepare("SELECT * FROM service WHERE svc_code=:target");
                    $pdo_statement->execute([':target'=>$this->svc_code]);
                    $result = $pdo_statement->fetch();
        
                    if($result > 0){
                        return 1;
                    }else{
                        return 0;
                    }
        }

        function addService($conn, $targetDept){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }

            if($this->checkSvcCode($conn) == 0 && $targetDept->checkDeptCode($conn) > 0){
                $pdo_statement = $conn->prepare("INSERT INTO
                    service(svc_code, svc_desc, svc_enable, dept_code)
                    VALUES (:svc_code, :svc_desc, :svc_enable, :dept_code)");
            if($pdo_statement->execute([
                ':svc_code'=>$this->svc_code,
                ':svc_desc'=>$this->svc_desc,
                ':svc_enable'=>$this->svc_enable,
                ':dept_code'=>$targetDept->dept_code])){
                    return 1;
                }else{
                    return -1;
                }
            }else{
                return 0;
            }
        }
    }

    class Appointment{
        public $q_ID;
        public $personnel_ID;
        public $app_datetime;

        function __construct($id, $personnel_ID, $app_datetime){
            $this->q_ID = $id;
            $this->personnel_ID = $personnel_ID;
            $this->app_datetime = $app_datetime;
        }

        
    }

    class AppointmentQueue{
        public $app_datetime;
        public $svc_name;

        function __construct($datetime, $svc_name){
            $this->app_datetime = $datetime;
            $this->svc_name = $svc_name;
        }
    }

?>