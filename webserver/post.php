<?php
/**
 * KBS Post-API
 *
 * Empfaengt POST-Daten und leitet sie an einen oder mehrere Raspberry Pis weiter.
 * Wird von externen Systemen (z.B. Homeautomation, Skripte) als einfache
 * HTTP-Schnittstelle genutzt.
 *
 * POST-Parameter:
 *   line1   – Text fuer Zeile 1 (max. 16 Zeichen, optional, Default: Datum)
 *   line2   – Text fuer Zeile 2 (max. 16 Zeichen, optional, Default: Uhrzeit)
 *   bell    – Buzzer abspielen: "on" oder "off" (optional, Default: "off")
 *   display – Display blinken: "on" oder "off" (optional, Default: "off")
 *   person  – Zielperson: "K0"=alle, "K1"/"K2"/"K3"=einzeln (optional, Default: "K0")
 *
 * Antwort: JSON mit Ergebnis pro Ziel-Pi
 */
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

$datum   = date("d.m.Y");
$uhrzeit = date("H:i") . " Uhr";

// Eingaben auslesen und sanitisieren
$line1   = isset($_POST['line1'])   ? htmlspecialchars($_POST['line1'])   : $datum;
$line2   = isset($_POST['line2'])   ? htmlspecialchars($_POST['line2'])   : $uhrzeit;
$bell    = isset($_POST['bell'])    ? htmlspecialchars($_POST['bell'])    : "off";
$display = isset($_POST['display']) ? htmlspecialchars($_POST['display']) : "off";
$person  = isset($_POST['person'])  ? htmlspecialchars($_POST['person'])  : "K0";

// JSON-Payload fuer die Pi-API erstellen
$myObj = new stdClass();
$myObj->line1   = $line1;
$myObj->line2   = $line2;
$myObj->bell    = $bell;
$myObj->display = $display;

$content = json_encode($myObj);

// Ziel-URLs ueber zentrale Konfiguration ermitteln
$targets = kbs_get_targets($person);
$results = array();

foreach ($targets as $url) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL            => $url,
        CURLOPT_HEADER         => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => array("Content-type: application/json"),
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $content,
        CURLOPT_TIMEOUT        => KBS_CURL_TIMEOUT,
        CURLOPT_CONNECTTIMEOUT => KBS_CURL_CONNECT_TIMEOUT,
    ));
    $json_response = curl_exec($curl);
    $http_code  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($curl);
    curl_close($curl);

    $entry = array(
        'target'    => $url,
        'http_code' => $http_code,
        'success'   => ($http_code === 200 || $http_code === 201),
    );
    if ($curl_error) {
        $entry['error'] = $curl_error;
    }
    $results[] = $entry;
}

echo json_encode(array(
    'sent'    => $content,
    'person'  => $person,
    'results' => $results,
), JSON_PRETTY_PRINT);
?>