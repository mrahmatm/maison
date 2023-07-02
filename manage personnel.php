<?php

    $auth_type = "PER";
    include 'connect.php';
    include 'class.php';
    $q = $_REQUEST["method"];
    //$q = "checkStaff";
    if(strcmp($q,  "fetchAllPersonnel") == 0){
        $result = fetchAllPersonnel($conn);
        if(is_array($result)){
            header('Content-Type: application/json'); // Set the response header as JSON
            $result = json_encode($result);
        }
            
        echo $result;
    }

    if(strcmp($q,  "checkStaff") == 0){
        $id = $_REQUEST["id"];
        //$id = "master";
        $IC = $_REQUEST["IC"];
        //$IC = "1234";
        //$id = "SM123";
        echo personnelLogin($conn, $id, $IC);
    }

    if(strcmp($q,  "setAttend") == 0){
        $id = $_REQUEST["target"];
        $val = $_REQUEST["val"];
        $sql = "UPDATE personnel SET personnel_attend =:val WHERE personnel_id=:id";
        $pdo_statement = $conn->prepare($sql);
        
        if($pdo_statement->execute([':id'=>$id, ':val'=>$val])){
            echo 1;
        }else{
            echo 0;
        }

    }

?>