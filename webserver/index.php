<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KBS</title>
    <meta name="description" content="Source code generated using layoutit.com">
    <meta name="author" content="LayoutIt!">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
<!--	<script src="//code.jquery.com/jquery-3.3.1.js"></script> -->
	<script src="//code.jquery.com/jquery-3.3.1.min.js"></script>

	<script>
    var header = document.querySelector('header');
    var section = document.querySelector('section');

	var g_json = load_json();

	function populate (id){
		var i = document.getElementById('auswahl').value;
		document.getElementById('line1').value = g_json[i].Zeile1 ; 
		document.getElementById('line2').value = g_json[i].Zeile2 ;
		document.getElementById('bell').checked = (g_json[i].bell == "on") ? true : false;
		document.getElementById('display').checked = (g_json[i].display == "on") ? true : false;
	}

    function submitform(){
        if(document.myform.onsubmit &&
        !document.myform.onsubmit())
        {
            return;
        }
        document.myform.submit();
        return false;
    }

    function load_json() {
        var requestURL = 'texte.json';
        var request = new XMLHttpRequest();
        request.open('GET', requestURL);
        request.responseType = 'json';
        request.send();

        request.onload = function() {
            var jsonObj = request.response;
            showHeroes(jsonObj);
			return jsonObj;
        }
    }

    function showHeroes(jsonObj) {
      var heroes = jsonObj['members'];
	  g_json = jsonObj['members'];
	  var select = document.getElementById("auswahl");
	  select.innerHTML = "";

      for(var i = 0; i < heroes.length; i++) {
		var opt = heroes[i].Zeile1 + " " + heroes[i].Zeile2;
		var el = document.createElement("option");
		el.textContent = opt;
		el.value = i;
		select.appendChild(el);
		}
	}
</script>	​
	 
	
  </head>
  <body>
    <div class="container-fluid">
	<div class="row">
		<div class="col-md-4">
		</div>
		<div class="col-md-4">
			<p>&nbsp;</p><!--<img alt="Bootstrap Image Preview" src="https://www.layoutit.com/img/sports-q-c-140-140-3.jpg">-->
			<div class="page-header">
				<h1>KBS<br>
					<small>Kinder Benachrichtigungs System v1.1</small>
				</h1>
				<p>&nbsp;</p>
			</div>

<?php
    $datum = date("d.m.Y");
    $uhrzeit = date("H:i") . " Uhr";
    if(isset($_POST['submit']))   {
		if (isset($_POST['line1']))   { $line1   = htmlspecialchars($_POST['line1']);   } else {$line1 = "$datum";}
		if (isset($_POST['line2']))   { $line2   = htmlspecialchars($_POST['line2']);   } else {$line2 = "$uhrzeit";}
		if (isset($_POST['bell']))    { $bell    = htmlspecialchars($_POST['bell']);    } else {$bell = "off";}
		if (isset($_POST['display'])) { $display = htmlspecialchars($_POST['display']); } else {$display = "off";}
		if (isset($_POST['person'])) { $person = $_POST['person']; } else {$person = "";}
			$myObj-> line1 = "$line1";
			$myObj-> line2 = "$line2";
			$myObj-> bell = "$bell";
			$myObj-> display = "$display";
			$myObj-> person = "$person";

		$myJSON = json_encode($myObj);
		$content = $myJSON;
#        echo $myJSON;
		$status = null;
		if($person == "K0") {
			$url = "http://pi-zero1.fritz.box:8080/lcd/api/v1.0/lcds";
			$status = send_message_to_pi($url,$content);
			$url = "http://pi-zero2.fritz.box:8080/lcd/api/v1.0/lcds";
			$status .= send_message_to_pi($url,$content);
			$url = "http://pi-zero3.fritz.box:8080/lcd/api/v1.0/lcds";
			$status .= send_message_to_pi($url,$content);
		}
		elseif($person == "K1") {
			$url = "http://pi-zero1.fritz.box:8080/lcd/api/v1.0/lcds";
			$status = send_message_to_pi($url,$content);
		}
		elseif($person == "K2") {
			$url = "http://pi-zero2.fritz.box:8080/lcd/api/v1.0/lcds";
			$status = send_message_to_pi($url,$content);
		}
		elseif($person == "K3") {
			$url = "http://pi-zero3.fritz.box:8080/lcd/api/v1.0/lcds";
			$status = send_message_to_pi($url,$content);
		}else{}

    }else{
        $line1  = $datum;
        $line2  = $uhrzeit;
        $bell   = "off";
        $display= "off";
	}
	
function send_message_to_pi($url,$content){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
	$json_response = curl_exec($curl);
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE) ;
	curl_close($curl);
	switch ($status) {
		case 200 || 201:  # OK
			$response = "Data sent successfully to: <small>$url</small><br>";
			break;
		default: # Error
			$response = "ERROR SENDING DATA: <b>StatusCode:</b> " .$status."<br><b>URL:</b> $url<br><b>Content:</b><small>$content</small><br><b>Error:</b> " . curl_errno($curl) ."<br>"; 
			break;
	}
#	$response = json_decode($json_response, true); # nutze ich nicht
	return $response;
}

?>
<?php
if (isset($status)) {
	$search = "ERROR";
	if(preg_match("/{$search}/i", $status)) { # wenn error vorkommt war es ein Feher
		?>
			<!-- Alert red -->
			<div class="alert alert-dismissable alert-danger">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
			<h4>ERROR SENDING DATA</h4>
			<?php echo "<b>Status:</b> " .$status."<br>" ?>
		</div>
		<?php
	}else {
		?>
		<!-- Alert green -->
		<div class="alert alert-dismissable alert-success">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<h4>Data sent successfully!</h4>
				<?php echo "$status"; ?>
			</div>
			<?php
	}
}

?>
			<!-- Alert green 
			<div class="alert alert-dismissable alert-success">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<h4>Data</h4>
				<--?php echo "status = " .$status." , Text = $line1 - $line2<br>bell = $bell , display = $display , person = $person"; ?>
			</div>
-->			
			<label>Vorlagen:<br>
				<select name="auswahl" id="auswahl" onchange="populate(this.id)"> 
				 </select> 
			</label>
			<button onclick="populate()">Übernehmen</button> 
			

			<form role="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<div class="form-group">
					 <!-- TextZeile1 -->
					<label for="TextZeile1">
						Text:
					</label>
					<input type="text" class="form-control" maxlength=16 id="line1" name="line1" autocomplete="off" value="<?php echo $line1 ?>" style="">
					<input type="text" class="form-control" maxlength=16 id="line2" name="line2" autocomplete="off" value="<?php echo $line2 ?>" style="">
				</div>

				<fieldset class="form-group">
					<div class="row">
						<div class="col-md-6">
							<div class="form-check">
								<!-- bell -->
								<p><input type="checkbox" name="bell" id="bell" value="on" <?php if ($bell=="on") {echo"checked";} ?>> Klingel<br>
								<!-- display -->
								<input type="checkbox" name="display" id="display" value="on" <?php if ($display=="on") {echo"checked";} ?>> Blinken </p>
							</div>
							<button type="submit" name="submit" value="Send Command" class="btn btn-primary">An Display senden</button>
						</div>
						<div class="col-md-6">
							<!-- Zielperson -->
							<legend>Zielperson</legend>
	 						<div class="form-check">
								<label class="form-check-label"><input type="radio" class="form-check-input" name="person" id="person0" value="K0" <?php if ($person!="K1" || $person!="K2" ) {echo"checked";} ?> >Beide Kinder benachrichtigen</label>
							</div>
							<div class="form-check">
								<label class="form-check-label"><input type="radio" class="form-check-input" name="person" id="person1" value="K1" <?php if ($person=="K1") {echo"checked";} ?> >Ramona benachrichtigen</label>
							</div>
							<div class="form-check">
								<label class="form-check-label"><input type="radio" class="form-check-input" name="person" id="person2" value="K2" <?php if ($person=="K2") {echo"checked";} ?> >Denise benachrichtigen</label>
							</div>
							<div class="form-check ">
								<label class="form-check-label"><input type="radio" class="form-check-input" name="person" id="person3" value="K3" <?php if ($person=="K3") {echo"checked";} ?> >	Den Vater benachrichtigen</label>
							</div>
						</div>
				  </fieldset>

				<button type="submit" name="submit" value="Send Command" class="btn btn-primary">An Display senden</button>

			</form>
		</div>
		<div class="col-md-4">

		<div class="row">

		</div>


			<!-- Alert green 		
			<div class="alert alert-dismissable alert-success">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<h4>Alert!
				</h4> <strong>Warning!</strong> Best check yo self, you're not looking too good. <a href="#" class="alert-link">alert link</a>
			</div>
			<!-- Alert yellow 		
			<div class="alert alert-dismissable alert-warning">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<h4>Alert!
				</h4> <strong>Warning!</strong> Best check yo self, you're not looking too good. <a href="#" class="alert-link">alert link</a>
			</div>
			<!-- Alert red  		
			<div class="alert alert-dismissable alert-danger">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<h4>
					Alert!
				</h4> <strong>Warning!</strong> Best check yo self, you're not looking too good. <a href="#" class="alert-link">alert link</a>
			</div>
			-->
		</div>
	</div>
</div>


<!--    <script src="js/jquery.min.js"></script>-->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
  </body>
</html>