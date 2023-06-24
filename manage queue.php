<?php

    $auth_type = "PER";
    include 'connect.php';
    include 'class.php';
    $q = $_REQUEST["method"];
    if(strcmp($q,  "fetchAllQueue") == 0){
        $result = fetchAllQueue($conn);

        $SLQJSON = $result[0];
        $APQJSON = $result[1];
        $GPQJSON = $result[2];
    
        echo json_encode([$SLQJSON, $APQJSON, $GPQJSON]);
    }

?>