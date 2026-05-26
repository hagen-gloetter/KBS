<?php
/**
 * KBS – Kinder Benachrichtigungs System
 * Zentrale Konfigurationsdatei
 *
 * Alle netzwerkspezifischen Einstellungen (Hostnamen, Ports, Timeouts)
 * werden hier zentral verwaltet. Wenn sich Pi-Hostnamen oder die
 * Netzwerkstruktur aendern, muss nur diese Datei angepasst werden.
 */

/** API-Endpunkt-Pfad auf den Raspberry Pis */
define('KBS_API_PATH', '/lcd/api/v1.0/lcds');

/** API-Port der Flask-Server */
define('KBS_API_PORT', 8080);

/** cURL-Timeout fuer Requests in Sekunden */
define('KBS_CURL_TIMEOUT', 10);

/** cURL-Connect-Timeout in Sekunden */
define('KBS_CURL_CONNECT_TIMEOUT', 5);

/**
 * Raspberry Pi Ziele und Personenzuordnungen.
 *
 * Jeder Eintrag definiert:
 *   'name'  – Anzeigename der Person (wird in der UI angezeigt)
 *   'host'  – Hostname oder IP-Adresse des Raspberry Pi
 *   'emoji' – HTML-Entity des Emojis fuer die Anzeige
 *
 * Neue Personen/Pis koennen einfach hier ergaenzt werden (z.B. 'K4').
 * Die Keys (K1, K2, ...) werden als Werte der Radio-Buttons verwendet.
 * K0 ist reserviert fuer "Alle" und wird automatisch behandelt.
 */
$KBS_TARGETS = array(
    'K1' => array('name' => 'Ramona', 'host' => 'pi-zero1.fritz.box', 'emoji' => '&#x1F467;'),
    'K2' => array('name' => 'Denise', 'host' => 'pi-zero2.fritz.box', 'emoji' => '&#x1F467;'),
    'K3' => array('name' => 'Vater',  'host' => 'pi-zero3.fritz.box', 'emoji' => '&#x1F468;'),
);

/**
 * Erzeugt die vollstaendige API-URL fuer einen Pi-Host.
 *
 * @param string $host  Hostname oder IP-Adresse
 * @return string       Vollstaendige URL (z.B. http://pi-zero1:8080/lcd/api/v1.0/lcds)
 */
function kbs_api_url($host) {
    return 'http://' . $host . ':' . KBS_API_PORT . KBS_API_PATH;
}

/**
 * Gibt die API-URLs fuer eine Zielperson zurueck.
 *
 * @param string $person  Personen-Key: 'K0' fuer alle, 'K1'/'K2'/... fuer einzeln
 * @return array          Liste der API-URLs (leer bei unbekanntem Key)
 */
function kbs_get_targets($person) {
    global $KBS_TARGETS;
    if ($person === 'K0') {
        return array_map(function($t) { return kbs_api_url($t['host']); }, $KBS_TARGETS);
    }
    if (isset($KBS_TARGETS[$person])) {
        return array(kbs_api_url($KBS_TARGETS[$person]['host']));
    }
    return array();
}
