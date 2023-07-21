<?php
    
    $handle = fopen('file://' . __DIR__ . '/cache.txt', 'r');

    // You can use an infinite loop to keep going until you say stop.
    while(1){
    // This is where you get the next email.
    $line = fgets($handle);
    // If it is blank, the stream returns false, so you can break.
    if (!$line){
        break;
    }
    $explode = explode(",", $line);
    $current = (object)[
        "email" => $explode[0],
        "code"  => $explode[1]
    ];

    echo var_dump($current)."<br>";
    }

?>