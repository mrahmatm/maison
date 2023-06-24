<?php
    $auth_type = "PER";
    require 'connect.php';

    include 'class.php';
    //include 'function.php';
    //$latlng = "5.262336504724555, 103.16524533120848";
    //$latlng1 = "5.262512978963726, 103.1650855825122";
    //$status = setClinicLocation($conn, $latlng1);
    //$newPatient = new Patient("980623-10-9186", "JIYOUNG", "baek@gmail.com", "018-99932844");
    //$status = $newPatient->addPatient($conn);
    $newQueue = new Queue("APP", "367512-48-2241");
    $newQueue->setType("APP");
    //enqueue($conn, $newQueue)
    //$status = insertAppQueue($conn, $newQueue);
    $year = 2023;
    $month = 10;
    $date = 15;
    $hours = 21;
    $minutes = 00;

    $sqlDatetime = convertToSqlDatetime($year, $month, $date, $hours, $minutes);
    //insertAppQueue($conn, $newQueue, $sqlDatetime, "SM123");
    $testQueue = new Queue("APP", "367512-48-2241");
    $testQueue->setID("462");

    //$result = APPtoAPQ($conn, "289");
    //$result = insertLateCBQ($conn, $testQueue);
    $result = dequeue($conn, "SLQ");
    var_dump($result);

?>
