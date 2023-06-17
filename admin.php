<?php
    $auth_type = "PER";
    require 'connect.php';

    include 'class.php';

    $newDept = new Department($conn, "GEN", "General Unit", "General staffs only.");
    $newPersonnel = new Personnel($conn, "990612-10-3433", "YUUMA", "yuuma@hotmail.com", "019-2229182", "MA", "GEN", "GEN-2023-3");
    $newOperation = new Operation($newPersonnel, "SURGERY", "NAORTICS");
    $status = $newOperation->addOperation($conn, $newPersonnel);
    var_dump ($status);
?>