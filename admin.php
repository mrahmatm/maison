<?php
    $auth_type = "PER";
    require 'connect.php';

    include 'class.php';

    $status = setClinicCapacity($conn, 10);
    var_dump ($status);
?>