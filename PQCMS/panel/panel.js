let internalLinks = document.querySelectorAll('.internalLink')
let main = document.querySelector("#panelMain");

// Aktualizacja bloku panelu po załądowaniu strony
document.addEventListener('DOMContentLoaded', function() {
  if(!getCookie("main"))
    updateMain("home")
  else
    updateMain(getCookie("main"))
})

// Funkcjonalność linków - podmiana bloku panelu
internalLinks.forEach(e => {
    e.addEventListener('click',function() {
        updateMain(e.getAttribute("internalLink"))
    })
    e.addEventListener('keypress',function(event) {
        if(event.keyCode === 13 || event.key === "Enter")
            updateMain(e.getAttribute("internalLink"))
    })
})

/**
 * Funkcja aktualizująca główny blok panelu
 * @param {string} path ścieżka pliku, która będzie wyświetlana w panelu
 */
function updateMain(path) {
    main.src = path;
    setCookie("main",path,0,0,30);
}


// COOKIES

/**
 * Funkcja zapisująca ciasteczko
 * @param {string} cname nazwa
 * @param {string} cvalue wartość
 * @param {int} exdays wygasa w dniach
 * @param {int} exhours wygasa w godzinach
 * @param {int} exminutes wygasa w minutach
 */
function setCookie(cname, cvalue, exdays, exhours, exminutes) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000) + (exhours * 60 * 60 * 1000) + (exminutes * 60 * 1000));
    let expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
  }
  
/**
 * Zwraca wartość ciasteczka (lub null)
 * @param {string} cname nazwa ciastka
 * @returns string|null ciasteczko
 */
function getCookie(cname) {
    let name = cname + "="
    let ca = document.cookie.split(';')
    for(let i = 0; i < ca.length; i++) 
    {
      let c = ca[i];
      while (c.charAt(0) == ' ')
        c = c.substring(1)
      if (c.indexOf(name) == 0)
        return c.substring(name.length, c.length)
    }
    return null;
}

// function checkAuthKeyValidity()
// {
//     var xmlHttp = new XMLHttpRequest();
//
//     // xmlHttp.setRequestHeader("Content-Type", "application/json");
//     // var postData = JSON.stringify({ domain: window.location.hostname, klucz2: "wartosc2" });
//
//     // TODO do zmiany! Na razie jak działam na localhoscie tak musi być, ale na prodzie zmienić na 2.
//     xmlHttp.open( "GET", window.location.origin+"/pqcms/pqcms/panel/scripts/IsValidUserSession.php", true ); // false for synchronous request
//     // xmlHttp.open( "GET", window.location.origin+"/pqcms/panel/scripts/IsValidUserSession.php", true ); // false for synchronous request
//     xmlHttp.send( null );
//     return xmlHttp.responseText;
// }

function checkAuthKeyValidity() {
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open("GET", window.location.origin + "/pqcms/pqcms/panel/scripts/IsValidUserSession.php", true);

    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState === 4) {
            if (xmlHttp.status === 200) {
                console.log(xmlHttp.responseText);
            } else {
                console.error("Błąd żądania: " + xmlHttp.statusText);
            }
        }
    };

    xmlHttp.send(null);
    return xmlHttp.responseText;
}

// let ar = JSON.parse(httpGet("http://localhost/electrocms/server/test/test.php"));
// let ar = JSON.parse(httpGet("http://localhost/pqcms/server/GetVersion.php"));
// console.log(ar);