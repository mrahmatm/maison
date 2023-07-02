<?php
    $auth_type = "PER";
    require 'connect.php';

    include 'class.php';
    $newQueue = new Queue("GPQ", "546218-51-8937");
    //$newQueue->setID("74");
    $year = 2023;
    $month = 10;
    $date = 15;
    $hours = 21;
    $minutes = 00;

    $sqlDatetime = convertToSqlDatetime($year, $month, $date, $hours, $minutes);
    enqueue($conn, $newQueue, "GPQ");
    //insertAppQueue($conn, $newQueue, $sqlDatetime);
    //APPtoAPQ($conn, "998");
    //$status = progressQueue($conn);
    //$status = dequeue($conn, "APQ");
    //var_dump($status);
?>
