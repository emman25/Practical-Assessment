<?php
date_default_timezone_set('UTC');

function getUsersWhoDontWantAlerts(){

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://interview-assessment-1.realmdigital.co.za/do-not-send-birthday-wishes",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "",
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
        exit(1);
    } 

    $dataResponse = json_decode($response, true);

    return $dataResponse;
}

function processBirthday($employees){
    foreach($employees as $employee){
        if(array_key_exists("dateOfBirth", $employee)){
            
            if(hasBirthday($employee["dateOfBirth"])){
                sendEmail($employee["name"]);
            }
        }
    }   
}

function hasBirthday($birthday){
    try {
        $curr_time = strtotime(date('m/d/Y h:i:s a', time()));
        $cday = getdate($curr_time)["mday"];
        $cmon = getdate($curr_time)["mon"];
        $cyear = getdate($curr_time)["year"];

        $emp_time = strtotime($birthday); 
        $bday = getdate($emp_time)["mday"];
        $bmon = getdate($emp_time)["mon"];

        if(isLeapYear($cyear)){
            if(($bday == $cday) && ($bmon == $cmon)){
                return true;
            }
        } else {
            if (($bday == 29 && $bmon == 2) and ($cmon == 3 && $cday==1)){
                return true;
            } else {
            
                if(($bday == $cday) && ($bmon == $cmon)){
                    return true;
                }
            }
        }

        return false;
    } catch (\Throwable $th) {
        return false;
    }
}

function sendEmail($name){

    $message = "Happy Birthday: $name";
    echo json_encode($message);

    return;
}

function getValidEmployees($employees){
    $selectedEmployees = array();
    foreach($employees as $employee){
        if(array_key_exists("employmentEndDate", $employee)){
            if($employee["employmentEndDate"] == null){
                array_push($selectedEmployees, $employee);
            }
        }
    }

    return $selectedEmployees;
}

function getCurrentWorkingEmployees($employees){
    $selectedEmployees = array();
    foreach($employees as $employee){
        if(array_key_exists("employmentStartDate", $employee)){
            $emp_time = strtotime($employee["employmentStartDate"]); 
             
            $curr_time = strtotime(date('m/d/Y h:i:s a', time()));

            if($emp_time <= $curr_time){
                array_push($selectedEmployees, $employee);
            }
        }
    }

    return $selectedEmployees;
}

function getEmployeesWithConfiguredBirthday($employees){
    $selectedEmployees = array();
    $usersExclude = getUsersWhoDontWantAlerts();

    foreach($employees as $employee){
        if(array_key_exists("id", $employee)){
            if(!in_array($employee["id"] ,$usersExclude)){
                array_push($selectedEmployees, $employee);
            }
        }
    }

    return $selectedEmployees;
}

function getEmployees(){
 
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://interview-assessment-1.realmdigital.co.za/employees",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "",
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
        exit(1);
    } 

    $dataResponse = json_decode($response, true);

    return $dataResponse;
}

function isLeapYear($year) {
   if ($year % 400 == 0){
       return true;
   }
      
   if ($year % 4 == 0) {
       return true;
    }
   
    return false;
}