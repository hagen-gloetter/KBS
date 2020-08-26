
<?php 
# https://mdn.github.io/learning-area/javascript/oojs/json/heroes-finished.html
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">

    <title>KBS Edit</title>

    <link href="https://fonts.googleapis.com/css?family=Righteous" rel="stylesheet"> 
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
            var superHeroes = request.response;
            populateHeader(superHeroes);
            showHeroes(superHeroes);
            }
    }

    function populateHeader(jsonObj) {
      var myH1 = document.createElement('h1');
      myH1.textContent = jsonObj['squadName'];
//      header.appendChild(myH1);

      var myPara = document.createElement('p');
      myPara.textContent = 'Hometown: ' + jsonObj['homeTown'] + ' // Formed: ' + jsonObj['formed'];
//      header.appendChild(myPara);

    }

    function showHeroes(jsonObj) {
      var heroes = jsonObj['members'];

      for(var i = 0; i < heroes.length; i++) {
        var myArticle = document.createElement('article');
        var myH2 = document.createElement('h2');
        var myPara1 = document.createElement('p');
        var myPara2 = document.createElement('p');
        var myPara3 = document.createElement('p');
        var myPara4 = document.createElement('p');

        myPara1.textContent = 'Zeile1: ' + heroes[i].Zeile1;
        myPara2.textContent = 'Zeile2: ' + heroes[i].Zeile2;
        myPara3.textContent = 'bell:' + heroes[i].bell;
        myPara4.textContent = 'blink:' + heroes[i].display;

        myH2.textContent = "Element " +i+ ":";

        var f1   = document.createElement("input");
        f1.type  = "text";
        f1.name  = "Zeile1-" + i;
        f1.id    = "Zeile1-" + i;
        f1.value = heroes[i].Zeile1;

        var f2 = document.createElement("input");
        f2.type = "text";
        f2.name = "Zeile2-" + i;
        f2.id   = "Zeile2-" + i;
        f2.value = heroes[i].Zeile2;

        var f3 =  document.createElement('input');
        f3.type = "checkbox";
        f3.name = "bell-" + i;
        f3.id   = "bell-" + i;
        f3.checked = (heroes[i].bell == "on") ? true : false;

        var f4 =  document.createElement('input');
        f4.type = "checkbox";
        f4.name = "display-" + i;
        f4.id   = "display-" + i;
        f4.checked = (heroes[i].display == "on") ? true : false;

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
