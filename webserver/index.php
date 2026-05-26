<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KBS – Kinder Benachrichtigungs System</title>
    <meta name="description" content="Kinder Benachrichtigungs System – Nachrichten an LCD-Displays senden">
    <link href="css/style.css" rel="stylesheet">
  </head>
  <body>

<?php
    /**
     * KBS – Kinder Benachrichtigungs System
     * Haupt-Weboberflaeche zum Senden von Nachrichten an LCD-Displays.
     *
     * Funktionsweise:
     * 1. Benutzer waehlt Vorlage oder gibt Text manuell ein
     * 2. Optionen (Klingel, Blinken) und Zielperson werden gewaehlt
     * 3. POST-Request sendet JSON an die Raspberry Pi Flask-APIs
     * 4. Ergebnis wird als Erfolgs-/Fehlermeldung angezeigt
     */
    require_once __DIR__ . '/config.php';

    $datum = date("d.m.Y");
    $uhrzeit = date("H:i") . " Uhr";
    $status = null;

    if(isset($_POST['submit'])) {
        // Formulardaten auslesen und sanitisieren
        $line1   = isset($_POST['line1'])   ? htmlspecialchars($_POST['line1'])   : $datum;
        $line2   = isset($_POST['line2'])   ? htmlspecialchars($_POST['line2'])   : $uhrzeit;
        $bell    = isset($_POST['bell'])    ? htmlspecialchars($_POST['bell'])    : "off";
        $display = isset($_POST['display']) ? htmlspecialchars($_POST['display']) : "off";
        $person  = isset($_POST['person'])  ? htmlspecialchars($_POST['person'])  : "";

        // JSON-Payload fuer die Pi-API erstellen
        $myObj = new stdClass();
        $myObj->line1   = $line1;
        $myObj->line2   = $line2;
        $myObj->bell    = $bell;
        $myObj->display = $display;

        $content = json_encode($myObj);

        // Ziel-URLs ueber zentrale Konfiguration ermitteln
        $targets = kbs_get_targets($person);
        foreach ($targets as $url) {
            $status .= send_message_to_pi($url, $content);
        }
    } else {
        $line1   = $datum;
        $line2   = $uhrzeit;
        $bell    = "off";
        $display = "off";
        $person  = "";
    }

/**
 * Sendet eine Nachricht per cURL POST an einen Raspberry Pi.
 *
 * @param string $url      Ziel-URL der Pi Flask-API
 * @param string $content  JSON-kodierter Nachrichteninhalt
 * @return string          HTML-Alert (Erfolg oder Fehler)
 */
function send_message_to_pi($url, $content) {
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

    if ($http_code === 200 || $http_code === 201) {
        return '<div class="alert alert-success">'
             . '<span><strong>Gesendet!</strong> Nachricht an <code>' . htmlspecialchars($url) . '</code> erfolgreich.</span>'
             . '<button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button></div>';
    }
    return '<div class="alert alert-danger">'
         . '<span><strong>Fehler!</strong> StatusCode: ' . intval($http_code)
         . ' – ' . htmlspecialchars($curl_error)
         . ' (<code>' . htmlspecialchars($url) . '</code>)</span>'
         . '<button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button></div>';
}
?>

    <div class="container">

      <div class="header">
        <h1>&#x1F4E2; KBS</h1>
        <p>Kinder Benachrichtigungs System</p>
      </div>

      <?php if ($status) { echo $status; } ?>

      <div class="card">
        <div class="card-body">
          <label for="auswahl"><strong>&#x1F4CB; Vorlage wählen:</strong></label>
          <div class="input-group">
            <select class="form-select" id="auswahl">
              <option selected disabled>Bitte wählen...</option>
            </select>
            <button class="btn btn-outline" type="button" id="btn-vorlage">Übernehmen</button>
          </div>
        </div>
      </div>

      <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <div class="card">
          <div class="card-header">&#x1F4DD; Nachricht</div>
          <div class="card-body">
            <div class="form-group">
              <label for="line1">Zeile 1 <small>(max. 16 Zeichen)</small></label>
              <input type="text" class="form-control lcd-input" maxlength="16" id="line1" name="line1" autocomplete="off"
                     value="<?php echo htmlspecialchars($line1); ?>" placeholder="Erste Zeile...">
            </div>
            <div class="form-group">
              <label for="line2">Zeile 2 <small>(max. 16 Zeichen)</small></label>
              <input type="text" class="form-control lcd-input" maxlength="16" id="line2" name="line2" autocomplete="off"
                     value="<?php echo htmlspecialchars($line2); ?>" placeholder="Zweite Zeile...">
            </div>
            <div class="switch-row">
              <label class="toggle">
                <input type="checkbox" name="bell" id="bell" value="on"
                       <?php if ($bell==="on") echo "checked"; ?>>
                <span class="toggle-track"></span>
                &#x1F514; Klingel
              </label>
              <label class="toggle">
                <input type="checkbox" name="display" id="display" value="on"
                       <?php if ($display==="on") echo "checked"; ?>>
                <span class="toggle-track"></span>
                &#x1F4A1; Blinken
              </label>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">&#x1F464; Zielperson</div>
          <div class="card-body">
            <div class="radio-group">
              <label class="radio-option">
                <input type="radio" name="person" value="K0"
                       <?php if (!isset($KBS_TARGETS[$person])) echo "checked"; ?>>
                <span class="radio-dot"></span>
                &#x1F46A; Alle Kinder
              </label>
              <?php foreach ($KBS_TARGETS as $key => $target): ?>
              <label class="radio-option">
                <input type="radio" name="person" value="<?php echo $key; ?>"
                       <?php if ($person === $key) echo "checked"; ?>>
                <span class="radio-dot"></span>
                <?php echo $target['emoji'] . ' ' . htmlspecialchars($target['name']); ?>
              </label>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <button type="submit" name="submit" value="Send Command" class="btn btn-primary">
          &#x1F4E8; An Display senden
        </button>
      </form>

      <div class="footer">KBS v3.0</div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var templates = [];
        fetch('texte.json')
            .then(function(res) { return res.json(); })
            .then(function(data) {
                templates = data.members || [];
                var select = document.getElementById('auswahl');
                templates.forEach(function(item, i) {
                    var option = document.createElement('option');
                    option.value = i;
                    option.textContent = item.Zeile1.trim() + ' \u2013 ' + item.Zeile2.trim();
                    select.appendChild(option);
                });
            })
            .catch(function(err) {
                console.error('Fehler beim Laden der Vorlagen:', err);
            });

        function applyTemplate() {
            var i = parseInt(document.getElementById('auswahl').value);
            if (isNaN(i) || !templates[i]) return;
            document.getElementById('line1').value = templates[i].Zeile1;
            document.getElementById('line2').value = templates[i].Zeile2;
            document.getElementById('bell').checked = (templates[i].bell === 'on');
            document.getElementById('display').checked = (templates[i].display === 'on');
        }

        document.getElementById('btn-vorlage').addEventListener('click', applyTemplate);
        document.getElementById('auswahl').addEventListener('change', applyTemplate);
    });
    </script>
  </body>
</html>