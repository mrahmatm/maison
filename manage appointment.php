<?php
    $auth_type = "PER";
    include 'connect.php';
    include 'class.php';
    
    $q = $_REQUEST["method"];
    
    if ($q == "fetchAllAppointment") {
        // Fetch service data
        $result = fetchAllAppointment($conn);
        if ($result === null || $result == null) {
            $result = null;
        } else {
            $result = json_encode($result);
        }
        echo $result;
    }
?>
