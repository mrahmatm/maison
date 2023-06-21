<?php
    $auth_type = "PER";
    include 'connect.php';
    include 'class.php';
    //include 'function.php';

    $queue6 = new Queue("TTT", '432819-62-1752');
    $queue7 = new Queue("GEN", '432819-62-1752');
    //$queue7->setID("10");
    //enqueue($conn, $queue7);
    //$test = readQueueInstance($conn, $queue7->q_type);
    //$length = $test->getSize();
    //echo $test->toString();
    //$status = insertLateCBQ($conn, $queue7);

    $queueList = [];
    $newLinkedList = new LinkedList();

    $head = new Queue('SLQ', '000927-10-2521');
    $queueList[] = $head;
    
    $queue1 = new Queue('SLQ', '126834-95-5379');
    $queueList[] = $queue1;
    
    $queue2 = new Queue('SLQ', '367512-48-2241');
    $queueList[] = $queue2;
    
    $queue3 = new Queue('SLQ', '432819-62-1752');
    $queueList[] = $queue3;
    
    $queue4 = new Queue('SLQ', '479513-07-2885');
    $queueList[] = $queue4;
    
    $queue5 = new Queue('SLQ', '546218-51-8937');
    $queueList[] = $queue5;
    
    $queue6 = new Queue('SLQ', '745129-27-9823');
    $queueList[] = $queue6;
    
    $queue8 = new Queue('SLQ', '821347-37-5593');
    $queueList[] = $queue8;
    
    $queue10 = new Queue('SLQ', '934826-15-6732');
    $queueList[] = $queue10;
    
    $queue11 = new Queue('SLQ', '990925-10-9856');
    $queueList[] = $queue11;
    
    // Displaying the generated objects
    /*foreach ($queueList as $queue) {
        $newLinkedList->insertAtTail($queue);
    }*/
    //753916-73-3625
    $queue7 = new Queue('TTT', '934826-15-6732');

    $status = refreshCBQPreset($conn);
    echo "before insertion<br>";
    $newLinkedList = readQueueInstance($conn, "TTT");
    if($newLinkedList instanceof LinkedList)
        $newLinkedList->displayAllForward();
    else
        var_dump($newLinkedList);

    echo "after insertion<br>";

    $status = enqueue($conn, $queue7);
    echo"<br>status: ".var_dump($status)."<br>";
    $renewedList = readQueueInstance($conn, "TTT");
    if($renewedList instanceof LinkedList)
        $renewedList->displayAllForward();
    else
        var_dump($renewedList);

?>