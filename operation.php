<?php
    $auth_type = "PER";
    require 'connect.php';

    include 'class.php';
    //include 'function.php';
    $latlng = "5.262336504724555, 103.16524533120848";
    $latlng1 = "5.262512978963726, 103.1650855825122";
    $status = setClinicLocation($conn, $latlng1);

    var_dump ($status);
    
?>
