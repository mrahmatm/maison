<?php
    $auth_type = "PER";
    require 'connect.php';

    include 'class.php';

    $newDept = new Department("GEN", "General Unit", "General staffs only.");
    $newPersonnel = new Personnel($conn, "990612-10-3433", "YUUMA", "yuuma@hotmail.com", "019-2229182", "MA", "GEN", "GEN-2023-3");
    $newOperation = new Operation($newPersonnel, "SURGERY", "NAORTICS");
    $newService = new Service("MEW", "Daily checkups.", 2.0);
    //$newService->addService($conn, $newDept);
    $status = $newService->addService($conn, $newDept);
    var_dump ($status);
?>