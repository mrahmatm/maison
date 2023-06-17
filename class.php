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

        function addPatient($conn, $newPatient){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }
            if($newPatient->checkICNum($conn) == 0){
                $pdo_statement = $conn->prepare("INSERT INTO
                    patient(patient_ICNum, patient_name, patient_gender, patient_age, patient_email, patient_phoneNum)
                    VALUES (:patient_ICNum, :patient_name, :patient_gender, :patient_age, :patient_email, :patient_phoneNum)");
            if($pdo_statement->execute([
                ':patient_ICNum'=>$newPatient->patient_ICNum,
                ':patient_name'=>$newPatient->patient_name,
                ':patient_gender'=>$newPatient->patient_gender,
                ':patient_age'=>$newPatient->patient_age,
                ':patient_email'=>$newPatient->patient_email,
                ':patient_phoneNum'=>$newPatient->patient_phoneNum]))
                return 1;
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
                $this->personnel_ID = derivePersonnelID($dept_code, $tempHeadCount);
            }else{
                $this->personnel_ID = $personnel_ID;
            }
        }

        function addPersonnel($conn, $newPersonnel){
            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
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

            $pdo_statement = $conn->prepare("SELECT * FROM department WHERE dept_code=:targetCode");
            $pdo_statement->execute([':targetCode'=>$this->dept_code]);
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
                ':dept_headCount'=>0]))
                $newDept->refreshDeptHeadCount($conn);
                return 1;
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
                    return $count;
                }else{
                    $sql = "UPDATE department SET dept_headCount=:newCount WHERE dept_code = :targetValue";
                    $pdo_statement = $conn->prepare($sql);
                    $pdo_statement->execute([':newCount'=>0, ':targetValue'=>$this->dept_code]);
                    return 0;
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
    }

    class Service{
        public $svc_code;
        public $svc_desc;
        public $svc_fee;
        public $dept_code;

        function __construct($code, $desc, $fee, $dept_code){
            $this->svc_code = $code;
            $this->svc_desc = $desc;
            $this->svc_fee = $fee;
            $this->dept_code = $dept_code;
        }

        function addService($conn){
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
                ':dept_headCount'=>0]))
                $newDept->refreshDeptHeadCount($conn);
                return 1;
            }else{
                return 0;
            }
        }
    }
?>