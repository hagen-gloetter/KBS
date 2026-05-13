<?php
// KBS Post-API — Empfaengt POST-Daten und leitet sie an einen Raspberry Pi weiter
header('Content-Type: application/json');

$datum   = date("d.m.Y");
$uhrzeit = date("H:i") . " Uhr";

$line1   = isset($_POST['line1'])   ? htmlspecialchars($_POST['line1'])   : $datum;
$line2   = isset($_POST['line2'])   ? htmlspecialchars($_POST['line2'])   : $uhrzeit;
$bell    = isset($_POST['bell'])    ? htmlspecialchars($_POST['bell'])    : "off";
$display = isset($_POST['display']) ? htmlspecialchars($_POST['display']) : "off";

$myObj = new stdClass();
$myObj->line1   = $line1;
$myObj->line2   = $line2;
$myObj->bell    = $bell;
$myObj->display = $display;

$content = json_encode($myObj);
$url = "http://pi-zero1.fritz.box:8080/lcd/api/v1.0/lcds";

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL            => $url,
    CURLOPT_HEADER         => false,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => array("Content-type: application/json"),
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $content,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_CONNECTTIMEOUT => 5,
));
$json_response = curl_exec($curl);
$http_code  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$curl_error = curl_error($curl);
curl_close($curl);

$result = array(
    'sent'       => $content,
    'target'     => $url,
    'http_code'  => $http_code,
    'success'    => ($http_code === 200 || $http_code === 201),
);
if ($curl_error) {
    $result['error'] = $curl_error;
}

echo json_encode($result, JSON_PRETTY_PRINT);
?>