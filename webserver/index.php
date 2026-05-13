<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KBS – Kinder Benachrichtigungs System</title>
    <meta name="description" content="Kinder Benachrichtigungs System – Nachrichten an LCD-Displays senden">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
  </head>
  <body>

<?php
    $datum = date("d.m.Y");
    $uhrzeit = date("H:i") . " Uhr";
    $status = null;

    if(isset($_POST['submit'])) {
        $line1   = isset($_POST['line1'])   ? htmlspecialchars($_POST['line1'])   : $datum;
        $line2   = isset($_POST['line2'])   ? htmlspecialchars($_POST['line2'])   : $uhrzeit;
        $bell    = isset($_POST['bell'])    ? htmlspecialchars($_POST['bell'])    : "off";
        $display = isset($_POST['display']) ? htmlspecialchars($_POST['display']) : "off";
        $person  = isset($_POST['person'])  ? htmlspecialchars($_POST['person'])  : "";

        $myObj = new stdClass();
        $myObj->line1   = $line1;
        $myObj->line2   = $line2;
        $myObj->bell    = $bell;
        $myObj->display = $display;

        $content = json_encode($myObj);

        $pi_targets = array(
            "K0" => array(
                "http://pi-zero1.fritz.box:8080/lcd/api/v1.0/lcds",
                "http://pi-zero2.fritz.box:8080/lcd/api/v1.0/lcds",
                "http://pi-zero3.fritz.box:8080/lcd/api/v1.0/lcds"
            ),
            "K1" => array("http://pi-zero1.fritz.box:8080/lcd/api/v1.0/lcds"),
            "K2" => array("http://pi-zero2.fritz.box:8080/lcd/api/v1.0/lcds"),
            "K3" => array("http://pi-zero3.fritz.box:8080/lcd/api/v1.0/lcds"),
        );

        if (isset($pi_targets[$person])) {
            foreach ($pi_targets[$person] as $url) {
                $status .= send_message_to_pi($url, $content);
            }
        }
    } else {
        $line1   = $datum;
        $line2   = $uhrzeit;
        $bell    = "off";
        $display = "off";
        $person  = "";
    }

function send_message_to_pi($url, $content) {
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

    if ($http_code === 200 || $http_code === 201) {
        return '<div class="alert alert-success alert-dismissible fade show" role="alert">'
             . '<strong>Gesendet!</strong> Nachricht an <code>' . htmlspecialchars($url) . '</code> erfolgreich.'
             . '<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
    }
    return '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
         . '<strong>Fehler!</strong> StatusCode: ' . intval($http_code)
         . ' – ' . htmlspecialchars($curl_error)
         . ' (<code>' . htmlspecialchars($url) . '</code>)'
         . '<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
}
?>

    <div class="container" style="max-width: 600px;">

      <!-- Header -->
      <div class="text-center mt-4 mb-4">
        <h1 class="display-4">&#x1F4E2; KBS</h1>
        <p class="lead text-muted">Kinder Benachrichtigungs System</p>
      </div>

      <!-- Status-Meldungen -->
      <?php if ($status) { echo $status; } ?>

      <!-- Vorlagen -->
      <div class="card mb-3">
        <div class="card-body">
          <label class="font-weight-bold" for="auswahl">&#x1F4CB; Vorlage wählen:</label>
          <div class="input-group">
            <select class="custom-select" id="auswahl">
              <option selected disabled>Bitte wählen...</option>
            </select>
            <div class="input-group-append">
              <button class="btn btn-outline-secondary" type="button" id="btn-vorlage">Übernehmen</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Formular -->
      <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <div class="card mb-3">
          <div class="card-header font-weight-bold">&#x1F4DD; Nachricht</div>
          <div class="card-body">
            <div class="form-group">
              <label for="line1">Zeile 1 <small class="text-muted">(max. 16 Zeichen)</small></label>
              <input type="text" class="form-control" maxlength="16" id="line1" name="line1" autocomplete="off"
                     value="<?php echo htmlspecialchars($line1); ?>" placeholder="Erste Zeile...">
            </div>
            <div class="form-group">
              <label for="line2">Zeile 2 <small class="text-muted">(max. 16 Zeichen)</small></label>
              <input type="text" class="form-control" maxlength="16" id="line2" name="line2" autocomplete="off"
                     value="<?php echo htmlspecialchars($line2); ?>" placeholder="Zweite Zeile...">
            </div>
            <div class="form-row">
              <div class="col">
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input" name="bell" id="bell" value="on"
                         <?php if ($bell==="on") echo "checked"; ?>>
                  <label class="custom-control-label" for="bell">&#x1F514; Klingel</label>
                </div>
              </div>
              <div class="col">
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input" name="display" id="display" value="on"
                         <?php if ($display==="on") echo "checked"; ?>>
                  <label class="custom-control-label" for="display">&#x1F4A1; Blinken</label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-3">
          <div class="card-header font-weight-bold">&#x1F464; Zielperson</div>
          <div class="card-body">
            <div class="custom-control custom-radio">
              <input type="radio" class="custom-control-input" name="person" id="person0" value="K0"
                     <?php if ($person!=="K1" && $person!=="K2" && $person!=="K3") echo "checked"; ?>>
              <label class="custom-control-label" for="person0">&#x1F46A; Alle Kinder</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" class="custom-control-input" name="person" id="person1" value="K1"
                     <?php if ($person==="K1") echo "checked"; ?>>
              <label class="custom-control-label" for="person1">&#x1F467; Ramona</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" class="custom-control-input" name="person" id="person2" value="K2"
                     <?php if ($person==="K2") echo "checked"; ?>>
              <label class="custom-control-label" for="person2">&#x1F467; Denise</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" class="custom-control-input" name="person" id="person3" value="K3"
                     <?php if ($person==="K3") echo "checked"; ?>>
              <label class="custom-control-label" for="person3">&#x1F468; Vater</label>
            </div>
          </div>
        </div>

        <button type="submit" name="submit" value="Send Command" class="btn btn-primary btn-lg btn-block mb-4">
          &#x1F4E8; An Display senden
        </button>
      </form>

      <footer class="text-center text-muted mb-3">
        <small>KBS v2.0</small>
      </footer>
    </div>

    <script src="js/jquery-3.7.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    var g_json = [];

    $(document).ready(function() {
        $.getJSON('texte.json', function(data) {
            g_json = data.members || [];
            var $select = $('#auswahl');
            $.each(g_json, function(i, item) {
                $select.append($('<option>', {
                    value: i,
                    text: item.Zeile1.trim() + ' – ' + item.Zeile2.trim()
                }));
            });
        });

        $('#btn-vorlage').on('click', function() {
            var i = parseInt($('#auswahl').val());
            if (isNaN(i) || !g_json[i]) return;
            $('#line1').val(g_json[i].Zeile1);
            $('#line2').val(g_json[i].Zeile2);
            $('#bell').prop('checked', g_json[i].bell === 'on');
            $('#display').prop('checked', g_json[i].display === 'on');
        });

        // Vorlage auch bei Select-Änderung direkt übernehmen
        $('#auswahl').on('change', function() {
            $('#btn-vorlage').click();
        });
    });
    </script>
  </body>
</html>