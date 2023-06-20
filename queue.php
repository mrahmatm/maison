<?php
    $auth_type = "PER";
    include 'connect.php';
    include 'class.php';
    //include 'function.php';

    $queue6 = new Queue("TTT", '432819-62-1752');
    $queue7 = new Queue("GEN", '432819-62-1752');
    $queue7->setID("10");
    //enqueue($conn, $queue7);
    $test = dequeue($conn, $queue7->q_type);
    echo $test->toString();
    //var_dump($prepQueue);
?>