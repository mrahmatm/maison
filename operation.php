<?php
    $auth_type = "PER";
    require 'connect.php';

    include 'class.php';
    //include 'function.php';

    $newPatient = new patient('000927-10-2521', 'FROHZE', 'mrahmatm@gmail.com', '018-6962570');
    //$newPatient->patient_name='FROHZE';
    $updatePatient = new patient('000927-10-2521', 'SEKAI', 'mrahmatm@gmail.com', '018-6962570');
    
    $status = $newPatient->insertPatient($conn, $newPatient);

    var_dump ($status);
?>
