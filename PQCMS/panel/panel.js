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
 * Funkcja zapisująca ciasteckzo
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
 * Zwraca wartość ciasteczka (lub pusty null)
 * @param {string} cname 
 * @returns ciasteczko (lub null)
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