<?php

    $auth_type = "PER";
    include 'connect.php';
    include 'class.php';
    $q = $_REQUEST["method"];

    if(strcmp($q,  "getClinicLocation") == 0){
        $sql = "SELECT clinic_LatLng, clinic_maxRadius FROM clinic";
        $pdo_statement = $conn->prepare($sql);
        $pdo_statement->execute();
        $result=$pdo_statement->fetch(PDO::FETCH_OBJ);
        $latlng = $result->clinic_LatLng;
        
        $startIndex = strpos($latlng, "(") + 1;
        $endIndex = strpos($latlng, ")");
        $coordinates = explode(",", substr($latlng, $startIndex, $endIndex - $startIndex));
        $latitude = floatval(trim($coordinates[0]));
        $longitude = floatval(trim($coordinates[1]));

        $returnVal = array(
            'lat' => $latitude,
            'lng' => $longitude,
            'rad'=> $result->clinic_maxRadius
        );
        $jsonData = json_encode($returnVal);
        echo $jsonData;
    }

    if(strcmp($q,  "setClinicLocation") == 0){
        $latlng = $_REQUEST["currentLatLng"];
        $radius = $_REQUEST["newRadius"];
        $sql = "UPDATE clinic SET clinic_LatLng=:latlng, clinic_maxRadius=:maxradius";
        $pdo_statement = $conn->prepare($sql);
        $status = 1;
        $response = "Updated clinic location configs!";
        if(!$pdo_statement->execute([
            ':latlng' => $latlng,
            'maxradius' => $radius
        ])){
            $status = 0;
            $response = "Error updating location configs!";
        }
        $returnVal = array(
            'code' => $status,
            'response' => $response
        );
        $jsonData = json_encode($returnVal);
        echo $jsonData;
    }

    if(strcmp($q,  "fetchClinicCap") == 0){
        echo fetchClinicCap($conn);
    }
?>