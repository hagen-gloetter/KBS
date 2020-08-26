<?php
    $datum = date("d.m.Y");
	$uhrzeit = date("H:i") . " Uhr";
	$status = 200;
    if (isset($_POST['line1']))   { $line1   = htmlspecialchars($_POST['line1']);   } else {$line1 = "$datum";}
    if (isset($_POST['line2']))   { $line2   = htmlspecialchars($_POST['line2']);   } else {$line2 = "$uhrzeit";}
    if (isset($_POST['bell']))    { $bell    = htmlspecialchars($_POST['bell']);    } else {$bell = "off";}
    if (isset($_POST['display'])) { $display = htmlspecialchars($_POST['display']); } else {$display = "off";}
    if (isset($_POST['zielperson'])) { $display = htmlspecialchars($_POST['zielperson']); } else {$zielperson = "0";}
    $myObj-> line1 = "$line1";
    $myObj-> line2 = "$line2";
    $myObj-> bell  = "$bell";
    $myObj-> display = "$display";
    $myJSON = json_encode($myObj);
        echo $myJSON;
    
    $url = "http://pi-zero.fritz.box:80/lcd/api/v1.0/lcds";
#    $url = "http://pi-zero2.fritz.box:80/lcd/api/v1.0/lcds";
    $content = $myJSON;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
    $json_response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    #echo $status;
    if ( $status != 201 ) {
        #error
    }
    curl_close($curl);
    $response = json_decode($json_response, true);
    return $status;

?>