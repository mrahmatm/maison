<?php

    $auth_type = "PER";
    require 'connect.php';

    include 'class.php';

    $method = $_GET["method"];

    if($method == "generalInsertion" || $method === "generalInsertion"){
        $targetField = $_GET['targetField'];

        // Check the targetField value and handle the received parameters accordingly
        if ($targetField === "patient") {
            // Retrieve the patient section parameters
            $patient_ICNum = $_GET['patient_ICNum'];
            $patient_name = $_GET['patient_name'];
            $patient_phoneNum = $_GET['patient_phoneNum'];
            $patient_email = $_GET['patient_email'];
            
            $create = new Patient($patient_ICNum, $patient_name, $patient_email, $patient_phoneNum);
            $status = $create->addPatient($conn);
            $code = 1;
            $response = "Patient Inserted Successfully!";
            if($status!=1){
                if($status==0){
                    $code = 0;
                    $response = "IC Number already registered!";
                }
                if($status==-1){
                    $code = -1;
                    $response = "Error inserting patient!";
                }
            }

            $returnVal = array(
                'code' => $code,
                'response' => $response,
            );
            $jsonData = json_encode($returnVal);
            echo $jsonData;

        
        } elseif ($targetField === "appointment") {
            // Retrieve the appointment section parameters
            $patient_ICNumApp = $_GET['patient_ICNumApp'];
            $serviceDropdownApp = $_GET['serviceDropdownApp'];
            $inputDateApp = $_GET['inputDateApp'];
            $inputTimeApp = $_GET['inputTimeApp'];
            $personnel_ID = $_GET['personnel_ID'];
            
            $createQueue = new Queue("APP", $patient_ICNumApp, $serviceDropdownApp);
            $createQueue = checkResetQueueID($conn, $createQueue);
            $mergeDatetime = convertToSQLDateTimeHTML($inputDateApp, $inputTimeApp);
            $code = 1;
            $status = insertAppQueue($conn, $createQueue, $mergeDatetime, $personnel_ID);

            $response = "Appointment created!";
            if($status!=1){
                if($status==0){
                    $code = 0;
                    $response = "Error creating appointment!";
                }
            }

            $returnVal = array(
                'code' => $code,
                'response' => $response,
            );
            $jsonData = json_encode($returnVal);
            echo $jsonData;

        } elseif ($targetField === "personnel") {
            // Retrieve the personnel section parameters
            $inputPersonnelName = $_GET['inputPersonnelName'];
            $inputPersonnelICNumber = $_GET['inputPersonnelICNumber'];
            $inputPersonnelPhoneNumber = $_GET['inputPersonnelPhoneNumber'];
            $inputPersonnelEmail = $_GET['inputPersonnelEmail'];
            $departmentDropdownPersonnel = $_GET['departmentDropdownPersonnel'];
            $inputPersonnelType = $_GET['inputPersonnelType'];

            $create = new Personnel($conn, $inputPersonnelICNumber, $inputPersonnelName, $inputPersonnelEmail,
                $inputPersonnelPhoneNumber, $inputPersonnelType, $departmentDropdownPersonnel);
            $status = $create->addPersonnel($conn, $create);
            $code = 1;
            $response = "Personnel Inserted Successfully!";
            if($status!=1){
                if($status==-2){
                    $code = -2;
                    $response = "IC Number already registered!";
                }
                if($status==0){
                    $code = 0;
                    $response = "Error inserting personnel!";
                }
            }

            $returnVal = array(
                'code' => $code,
                'response' => $response,
            );
            $jsonData = json_encode($returnVal);
            echo $jsonData;
            
            // Perform actions with the personnel section parameters
            // ...
        } elseif ($targetField === "department") {
            // Retrieve the department section parameters
            $inputDepartmentCode = $_GET['inputDepartmentCode'];
            $inputDepartmentName = $_GET['inputDepartmentName'];
            $inputDepartmentDesc = $_GET['inputDepartmentDesc'];
            
            $create = new Department($inputDepartmentCode, $inputDepartmentName, $inputDepartmentDesc);
            $status = $create->addDepartment($conn, $create);
            $code = 1;
            $response = "Department Inserted Successfully!";
            if($status!=1){
                if($status==-1){
                    $code = -1;
                    $response = "Error inserting department!";
                }
                if($status==0){
                    $code = 0;
                    $response = "Department code already used!";
                }
            }

            $returnVal = array(
                'code' => $code,
                'response' => $response,
            );
            $jsonData = json_encode($returnVal);
            echo $jsonData;

            // Perform actions with the department section parameters
            // ...
        } elseif ($targetField === "service") {
            // Retrieve the service section parameters
            $inputServiceCode = $_GET['inputServiceCode'];
            $inputServiceDesc = $_GET['inputServiceDesc'];
            $departmentDropdownService = $_GET['departmentDropdownService'];
            $tempCreate = new Department($departmentDropdownService, null, null);
            $create = new Service($inputServiceCode, $inputServiceDesc, 1, $departmentDropdownService);
            $status = $create->addService($conn, $tempCreate);
            $code = 1;
            $response = "Service Inserted Successfully!";
            if($status!=1){
                if($status==-1){
                    $code = -1;
                    $response = "Error inserting department!";
                }
                if($status==0){
                    $code = 0;
                    $response = "Service code already used!";
                }
            }

            $returnVal = array(
                'code' => $code,
                'response' => $response,
            );
            $jsonData = json_encode($returnVal);
            echo $jsonData;

            // Perform actions with the service section parameters
            // ...
        }
    }

    if($method == "generalDeletion" || $method === "generalDeletion"){
        $type = $_GET["type"];
        $instance = $_GET["instance"];

        switch($type){
            case "patient":
                $pk = "patient_ICNum";
                break;
            case "personnel":
                $pk = "personnel_ID";
                break;
            case "department":
                $pk = "dept_code";
                $temp = new Department($instance, "temp", "temp");
                $code = $temp->deleteDepartment($conn, $temp);
                $response = "Deleted '".$instance."' successfully!";
                break;
            case "service":
                $pk = "svc_code";
                break;
            case "appointment":
                $pk = "q_ID";
                break;
            case "queue":
                $pk = "q_ID";
                break;
            default:
                $code -1;
                $response = "Error in deleting '".$instance."'!";
        }

        if(!isset($code) && $type!='department' && $type!=='department'){
            $sql = "DELETE FROM ".$type." WHERE ".$pk." = :targetInstance";
            $pdo_statement = $conn->prepare($sql);
            if($pdo_statement->execute([":targetInstance"=>$instance])){
                $code = 1;
                $response = "Deleted '".$instance."' successfully!";
            }
        }
        
        $returnVal = array(
            'code' => $code,
            'response' => $response,
        );
        $jsonData = json_encode($returnVal);
        echo $jsonData;
    }

    if($method == "generalModification" || $method === "generalModification"){
        
        $targetField = $_GET['targetField'];

        if ($targetField === 'patient') {
            // Receive values for patient section
            $patient_ICNum = $_GET['patient_ICNum'];
            $patient_name = $_GET['patient_name'];
            $patient_phoneNum = $_GET['patient_phoneNum'];
            $patient_email = $_GET['patient_email'];
    
            //retrieve original ID
            $originalID = $_GET['originalID'];

            if($conn == null){
                $auth_type = "PER";
                require 'connect.php';
            }

            $create = new Patient($patient_ICNum, $patient_name, $patient_email,
                $patient_phoneNum);
            //print("created patient: ".var_dump($create));

            if(strcmp($originalID, $patient_ICNum) == 0){
                //user tak ubah ic number
                $query = "UPDATE patient SET patient_name =:targetName, patient_phoneNum =:phone,
                    patient_email =:email, patient_age=:age, patient_gender=:gender WHERE patient_ICNum =:TargetIC";
                $pdo_statement = $conn->prepare($query);
                if($pdo_statement->execute([
                    ':targetName' => $patient_name,
                    ':phone' => $patient_phoneNum,
                    ':email' => $patient_email,
                    ':age' => $create->patient_age ? $create->patient_age : -1,
                    ':gender' => $create->patient_gender,
                    ':TargetIC' => $originalID
                ])){
                    $code = 1;
                    $response = "Patient Updated Successfully!";
                }else{
                    //error updating
                    $code =  -2;
                    $response = "Error updating patient!";
                }
            }else{
                //user ubah ic number
                if($create->checkICNum($conn) == 0){
                    //ic number baru available
                    $query = "UPDATE patient SET patient_ICNum =:ic, patient_name =:targetName, patient_phoneNum =:phone,
                        patient_email =:email, patient_age=:age, patient_gender=:gender  WHERE patient_ICNum =:TargetIC";
                $pdo_statement = $conn->prepare($query);
                if($pdo_statement->execute([
                    'ic' => $patient_ICNum,
                    ':targetName' => $patient_name,
                    ':phone' => $patient_phoneNum,
                    ':email' => $patient_email,
                    ':age' => $create->patient_age ? $create->patient_age : -1,
                    ':gender' =>$create->patient_gender,
                    ':TargetIC' => $originalID
                ])){
                    $code = 1;
                    $response = "Patient updated Successfully!";
                }else{
                    //error updating
                    $code =  -2;
                    $response = "Error updating patient!";
                }
                }else{
                    //ic number baru registered
                    $code = -1;
                    $response = "IC Number already registered on another patient!";
                }
            }

            // Return response
            $returnVal = array(
                'code' => $code,
                'response' => $response,
            );
            $jsonData = json_encode($returnVal);
            echo $jsonData;
        }
    
        if ($targetField === 'appointment') {
            // Receive values for appointment section
            $patient_ICNumApp = $_GET['patient_ICNumApp'];
            $serviceDropdownApp = $_GET['serviceDropdownApp'];
            $inputDateApp = $_GET['inputDateApp'];
            $inputTimeApp = $_GET['inputTimeApp'];
    
            // Perform further processing or validation as needed
    
            // Example: Insert/update data in the database
            // $sql = "UPDATE appointments SET service = '$serviceDropdownApp', date = '$inputDateApp', time = '$inputTimeApp' WHERE patient_ic_number = '$patient_ICNumApp'";
            // mysqli_query($connection, $sql);
    
            // Return response
            $response = "Appointment data updated successfully";
            $result = array('code' => 200, 'response' => $response);
            echo json_encode($result);
            exit;
        }
    
        if ($targetField === 'personnel') {
            //retrieve original ID
            $originalID = $_GET['originalID'];
            // Receive values for personnel section
            $inputPersonnelName = $_GET['inputPersonnelName'];
            $inputPersonnelICNumber = $_GET['inputPersonnelICNumber'];
            $inputPersonnelPhoneNumber = $_GET['inputPersonnelPhoneNumber'];
            $inputPersonnelEmail = $_GET['inputPersonnelEmail'];
            $departmentDropdownPersonnel = $_GET['departmentDropdownPersonnel'];
            $inputPersonnelType = $_GET['inputPersonnelType'];

            $create = new Personnel($conn, $inputPersonnelICNumber, $inputPersonnelName, $inputPersonnelEmail,
                $inputPersonnelPhoneNumber, $inputPersonnelType, $departmentDropdownPersonnel, $originalID);

            $query = "UPDATE personnel SET personnel_name =:targetName, personnel_ICNum =:ic, personnel_phoneNum =:phone, personnel_email =:email,
                personnel_type =:newType, dept_code=:newDept, personnel_age=:age, personnel_gender=:gender WHERE personnel_ID =:TargetID";
                $pdo_statement = $conn->prepare($query);
                if($pdo_statement->execute([
                    ':targetName' => $inputPersonnelName,
                    ':ic' => $inputPersonnelICNumber,
                    ':phone' => $inputPersonnelPhoneNumber,
                    ':email' => $inputPersonnelEmail,
                    ':newType' =>$inputPersonnelType,
                    ':newDept' =>$departmentDropdownPersonnel,
                    ':age' => $create->personnel_age ? $create->personnel_age : -1,
                    ':gender' => $create->personnel_gender,
                    ':TargetID' => $originalID
                ])){
                    $code = 1;
                    $response = "Personnel updated Successfully!";
                }else{
                    //error updating
                    $code =  -2;
                    $response = "Error updating personnel!";
                }
                
                // Return response
                $returnVal = array(
                    'code' => $code,
                    'response' => $response,
                );
                $jsonData = json_encode($returnVal);
                echo $jsonData;

        }
    
        if ($targetField === 'department') {
            // Receive values for department section
            $inputDepartmentCode = $_GET['inputDepartmentCode'];
            $inputDepartmentName = $_GET['inputDepartmentName'];
            $inputDepartmentDesc = $_GET['inputDepartmentDesc'];

            //retrieve original ID
            $originalID = $_GET['originalID'];

            $create = new Department($inputDepartmentCode, $inputDepartmentName, $inputDepartmentDesc);
            $flag = true;
            if(strcasecmp($originalID, $inputDepartmentCode) != 0){
                if($create->checkDeptCode($conn) != 0){
                    $code = -2;
                    $response = "Department code already in use!";
                    $flag = false;
                }
            }
            

            if(strcasecmp($originalID, $inputDepartmentCode) != 0 && $flag == true){
                //user changes dept_code
                $query = "UPDATE department SET dept_code=:code, dept_name=:name, dept_desc=:desc WHERE dept_code =:TargetID";
                $pdo_statement = $conn->prepare($query);
                if($pdo_statement->execute([
                    ':code' => $inputDepartmentCode,
                    ':name' => $inputDepartmentName,
                    ':desc' => $inputDepartmentDesc,
                    ':TargetID' => $originalID
                ])){
                    $code = 1;
                    $response = "Department updated Successfully!";
                }else{
                    //error updating
                    $code =  -2;
                    $response = "Error updating Department!";
                }
            }elseif($flag == true){
                //user did not change dept_code
                $query = "UPDATE department SET dept_name=:name, dept_desc=:desc WHERE dept_code =:TargetID";
                $pdo_statement = $conn->prepare($query);
                if($pdo_statement->execute([
                    ':name' => $inputDepartmentName,
                    ':desc' => $inputDepartmentDesc,
                    ':TargetID' => $originalID
                ])){
                    $code = 1;
                    $response = "Department updated Successfully!";
                }else{
                    //error updating
                    $code =  -2;
                    $response = "Error updating Department!";
                }
            }

                // Return response
                $returnVal = array(
                    'code' => $code,
                    'response' => $response,
                );
                $jsonData = json_encode($returnVal);
                echo $jsonData;
        }
    
        if ($targetField === 'service') {
            // Receive values for service section
            $inputServiceCode = $_GET['inputServiceCode'];
            $inputServiceDesc = $_GET['inputServiceDesc'];
            $departmentDropdownService = $_GET['departmentDropdownService'];
    
            //retrieve original ID
            $originalID = $_GET['originalID'];

            $create = new Service($inputServiceCode, $inputServiceDesc, 1 ,$departmentDropdownService);
            $flag = true;
            if(strcasecmp($originalID, $inputServiceCode) != 0){
                if($create->checkSvcCode($conn) != 0){
                    $code = -2;
                    $response = "Service code already in use!";
                    $flag = false;
                }
            }
            
            if(strcasecmp($originalID, $inputServiceCode) != 0 && $flag == true){
                //user changes svc_code
                $query = "UPDATE service SET svc_code=:code, svc_desc=:desc, svc_enable=:isEnable 
                    WHERE svc_code =:TargetID";
                $pdo_statement = $conn->prepare($query);
                if($pdo_statement->execute([
                    ':code' => $inputServiceCode,
                    ':desc' => $inputServiceDesc,
                    ':isEnable' => $create->svc_enable,
                    ':TargetID' => $originalID
                ])){
                    $code = 1;
                    $response = "Service updated Successfully!";
                }else{
                    //error updating
                    $code =  -2;
                    $response = "Error updating Service!";
                }
            }elseif($flag == true){
                //user did not change svc_code
                $query = "UPDATE service SET svc_desc=:desc, svc_enable=:isEnable 
                    WHERE svc_code =:TargetID";
                $pdo_statement = $conn->prepare($query);
                if($pdo_statement->execute([
                    ':desc' => $inputServiceDesc,
                    ':isEnable' => $create->svc_enable,
                    ':TargetID' => $originalID
                ])){
                    $code = 1;
                    $response = "Service updated Successfully!";
                }else{
                    //error updating
                    $code =  -2;
                    $response = "Error updating Service!";
                }
            }

                // Return response
                $returnVal = array(
                    'code' => $code,
                    'response' => $response,
                );
                $jsonData = json_encode($returnVal);
                echo $jsonData;
        }
    }

?>