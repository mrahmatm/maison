<?php
    $auth_type = "PER";
    include 'connect.php';
    include 'class.php';
    //include 'function.php';

    $status = checkQueueExists($conn, "GEN");

    $queue1 = new Queue("01", "GEN", '000927-10-2521');
    $queue2 = new Queue("02", "GEN", '934826-15-6732');
    $queue3 = new Queue("03", "GEN", '934826-15-6732');
    $queue4 = new Queue("04", "GEN", '821347-37-5593');
    $queue5 = new Queue("05", "GEN", '367512-48-2241');
    $queue6 = new Queue("06", "GEN", '432819-62-1752');

    $lane1 = new LinkedList();
    $lane1->insertAtHead($queue1);
    $lane1->insertAtTail($queue2);
    $lane1->insertAtTail($queue4);
    $lane1->insertAtTail($queue5);
    $lane1->insertAtTail($queue3);
    $lane1->insertAtTail($queue6);
    //$json = json_encode($lane1, JSON_PRETTY_PRINT);
    $lane1->displayForward();
    $size = $lane1->getSize();
    var_dump($size);
?>