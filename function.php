<?php
    //include 'class.php';

    function deriveAgeFromIC($target){

        if(strlen($target) <= 0 || strlen($target) > 14)
            return -1;

        if(strpos($target, "-") == FALSE)
            return -1;
        
        $arr = explode("-", $target);
        $birthDate = $arr[0];
        
        $correctedDate = "";
        $correctedDate .= $birthDate[4];
        $correctedDate .= $birthDate[5];
        $correctedDate .= $birthDate[2];
        $correctedDate .= $birthDate[3];
        $correctedDate .= $birthDate[0];
        $correctedDate .= $birthDate[1];
        
        $targetYear = (int) substr($correctedDate, strlen($correctedDate)-2, 2);
        $currentYear = date("y");
        $intCurrentYear = (int) $currentYear;
        
        //kiv
        if ($intCurrentYear < $targetYear){
            $correctedDate = substr_replace($correctedDate, "19", strlen($correctedDate)-2, 0);
        }else{
            $correctedDate = substr_replace($correctedDate, "20", strlen($correctedDate)-2, 0);
        }

        $correctedDate = substr_replace($correctedDate, "-", 2, 0);
        $correctedDate = substr_replace($correctedDate, "-", 5, 0);

        $birthDate = date_create($correctedDate);
        $nowDate = date_create(date('d-M-Y'));
        $dayDiff = date_diff($birthDate, $nowDate);

        return $dayDiff->y;
    }

    function deriveGenderFromIC($target){
        if(strlen($target) <= 0 || strlen($target) > 14)
            return -1;

        if(strpos($target, "-") == FALSE)
            return -1;
        
        $arr = explode("-", $target);
        $targetSegment = $arr[2];
        $targetChar = substr($targetSegment, -1);
        $targetDigit = (int) $targetChar;

        if($targetDigit % 2 == 0){
            return 'F';
        }else{
            return 'M';
        }

    }

    function derivePersonnelID($dept_code, $dept_headCount){
        $nowYear = date('Y');
        $strYear = strftime('Y', $nowYear);
        $output = $dept_code."-".$nowYear."-".$dept_headCount+1;

        return $output;
    }

    function checkQueueExists($conn, $type){
        if($conn == null){
            $auth_type = "PER";
            require 'connect.php';
        }
        
        $pdo_statement = $conn->prepare("SELECT * FROM lane WHERE lane_type=:target");
        $pdo_statement->execute([':target'=>$type]);
        $result = $pdo_statement->fetch(PDO::FETCH_LAZY);
        if($result != NULL)
            return 1;
        else
            return 0;
    }










?>