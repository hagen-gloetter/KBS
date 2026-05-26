
<?php 
# Vorlagen-Editor (Legacy) — zeigt texte.json zum Bearbeiten an
# Hinweis: Speichern ist nicht implementiert
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">

    <title>KBS Edit</title>

    <link rel="stylesheet" href="styles.css">
  </head>

  <body>

      <header>
      <h1> KBS Edit </h1>
      </header>
      <form name="myform" action="edit.php">
      <section>

      </section>
      <a href="javascript: submitform()">Submit</a>
    </form>
    <script>
    var header = document.querySelector('header');
    var section = document.querySelector('section');
            
    load_json();

    function submitform()
    {
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
            var jsonData = request.response;
            populateHeader(jsonData);
            showTemplates(jsonData);
            }
    }

    function populateHeader(jsonObj) {
      var myH1 = document.createElement('h1');
      myH1.textContent = jsonObj['AppName'] || 'KBS';

      var myPara = document.createElement('p');
      myPara.textContent = 'Vorlagen: ' + (jsonObj['members'] ? jsonObj['members'].length : 0);

    }

    function showTemplates(jsonObj) {
      var templates = jsonObj['members'];

      for(var i = 0; i < templates.length; i++) {
        var myArticle = document.createElement('article');
        var myH2 = document.createElement('h2');
        var myPara1 = document.createElement('p');
        var myPara2 = document.createElement('p');
        var myPara3 = document.createElement('p');
        var myPara4 = document.createElement('p');

        myPara1.textContent = 'Zeile1: ' + templates[i].Zeile1;
        myPara2.textContent = 'Zeile2: ' + templates[i].Zeile2;
        myPara3.textContent = 'bell:' + templates[i].bell;
        myPara4.textContent = 'blink:' + templates[i].display;

        myH2.textContent = "Vorlage " +i+ ":";

        var f1   = document.createElement("input");
        f1.type  = "text";
        f1.name  = "Zeile1-" + i;
        f1.id    = "Zeile1-" + i;
        f1.value = templates[i].Zeile1;

        var f2 = document.createElement("input");
        f2.type = "text";
        f2.name = "Zeile2-" + i;
        f2.id   = "Zeile2-" + i;
        f2.value = templates[i].Zeile2;

        var f3 =  document.createElement('input');
        f3.type = "checkbox";
        f3.name = "bell-" + i;
        f3.id   = "bell-" + i;
        f3.checked = (templates[i].bell == "on") ? true : false;

        var f4 =  document.createElement('input');
        f4.type = "checkbox";
        f4.name = "display-" + i;
        f4.id   = "display-" + i;
        f4.checked = (templates[i].display == "on") ? true : false;

        myArticle.appendChild(myH2);
        myArticle.appendChild(myPara1);
        myArticle.appendChild(myPara2);
        myArticle.appendChild(myPara3);
        myArticle.appendChild(myPara4);
        myArticle.appendChild(f1);
        myArticle.appendChild(f2);
        myArticle.appendChild(f3);
        myArticle.appendChild(f4);

        section.appendChild(myArticle);
      }
    }

    </script>
  </body>
</html>
