<?php
$authorized = FALSE;
if(!isset($auth_type)){
    echo '<script>alert("Please define authentication type!")</script>';
}elseif(strcmp($auth_type, "PER") == 0){
    //check if opertional admin
    $username = "operational_admin";
    $password = "150115";
    $authorized = TRUE;
}

$servername = "localhost";

if($authorized == TRUE){
    try {
        $conn = new PDO("mysql:host=$servername;dbname=maison", $username, $password);
      // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo "Connected successfully";
        echo '<script>alert("Connection SUCCESS!")</script>';
    } catch(PDOException $e) {
        //echo "Connection failed: " . $e->getMessage();
        echo '<script>alert("Connection FAILED, message: '.$e->getMessage().'")</script>';
    }
}

?>