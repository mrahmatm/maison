<?php

    $auth_type = "PER";
    include 'connect.php';
    include 'class.php';
    $q = $_REQUEST["method"];
    if(strcmp($q,  "fetchAllPatient") == 0){
        $result = fetchAllPatient($conn);
        if(is_array($result)){
            header('Content-Type: application/json'); // Set the response header as JSON
            $result = json_encode($result);
        }
            
        echo $result;
    }

?>