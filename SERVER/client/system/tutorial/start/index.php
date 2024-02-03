<?php

// TEGO NIE BLOKUJEMY!!!
// ZAWIERA INFO JAK W OGÓLE STARTOWAĆ PROGRAM PQCMS (ze zmianami config/data.json, więc serio podstawowe,
// jeszcze nie będzie miał(a) zweryfikowanej licencji a co dopiero wygenerowanego tokenu PQCMS)

?>

<!DOCTYPE html>
<html lang="pl-PL">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Poradnik - Start | PQCMS</title>
    <meta name="description" content="Oficjalna strona systemu PQCMS">
    <meta name="keywords" content="">
    <meta name="author" content='Wiktor "PoLeq" Soliński'>
    <meta http-equiv="X-Ua-Compatible" content="IE=edge">
    <link rel="icon" type="image/x-icon" href="../../../../images/PQCMS.svg">

    <link rel="stylesheet" href="../tutorial.css">
    <link rel="stylesheet" href="../../homepage.css">
    <link rel="stylesheet" href="../../../../bs5/css/bootstrap.min.css">

    <meta content="PQCMS" property="og:title"/>
    <meta content="(#1) Poradnik opisujący, jak połączyć Twój serwer z serwerami PQCMS."
          property="og:description"/>

</head>
<body>

    <noscript>
        UWAGA! Korzystanie ze strony z wyłączonym JavaScriptem uniemożliwia korzystanie ze strony w pełni pięknej i
        funkcjonalnej! Rób jak uważasz!
    </noscript>

    <div id="site-container" class="col-12 p-5">
        <h1>START</h1>
        Tutaj znajdziesz informacje, co zrobić przed pierwszym uruchomieniem PQCMS na swoim serwerze.
        <section>
            <div>
                Umieść pliki na serwerze tak, aby później uruchamiając link <div class="pre">https://twojadomena.pl/pqcms</div>
                uruchamiał się panel PQCMS
            </div>
        </section>
        <section>
            Podczas pierwszego uruchomienia aplikacji po wgraniu plików na serwer powinien ukazać się formularz z logowaniem:
            <img src="images/first_open.png" alt="Pierwsze uruchomienie aplikacji"/>
            Jednak nie będzie można się zalogować, ponieważ nie zostały zmienione odpowiednie dane do panelu.
            <b>Warto zaznaczyć, że jeśli uruchamiasz PQCMS po raz pierwszy na własną rękę, to nie musisz w tym wypadku
            kontaktować się z administratorem PQCMS. Jeśli jednak w przyszłości zobaczysz ten błąd, skontaktuj się z nami!</b>
        </section>
        <section>
            <div>
                Kolejnym krokiem będzie zmiana danych na Twoim serwerze. Uruchom aplikację umożliwiającą zamianę plików
                oraz przejść do lokalizacji <div class="pre">pqcms/config/files</div> oraz otwórz plik <div class="pre">data.json</div>.
                Powinien on wyglądać następująco:<br/>
                <i>(pole na prawo od "version" może się różnić)</i><br/>
            </div>
            <img src="images/data.json.png" alt="Plik data.json"/>
                Teraz musisz tylko uzupełnić dane podane przez administratora PQCMS!<br/>
                <i>Możesz już teraz zmienić dane do bazy danych: nie jest to wymagane, ale <u>zalecane!</u></i><br/>
                <pre>
{
    "database": {
        "host": "HOST_BD",
        "user": "NAZWA_UŻYTKOWNIKA_BD",
        "password": "HASŁO_BD",
        "name": "NAZWA_BD"
    },
    "pqcms": {
        "version": "dev-1.0.0",
        "domain": "DOMENA",
        "login": "LOGIN",
        "license_key": "KLUCZ_LICENCYJNY"
    }
}</pre>
            gdzie:
            <ul>
                <li>
                    <b>database</b> - dane do bazy danych
                    <ul>
                        <li><b>HOST_DB</b> - host bazy danych (np. "localhost", "poleq.pl")</li>
                        <li><b>NAZWA_UŻYTKOWNIKA_BD</b> - nazwa użytkownika bazy danych</li>
                        <li><b>HASŁO_BD</b> - hasło bazy danych</li>
                        <li><b>NAZWA_BD</b> - nazwa bazy danych</li>
                    </ul>
                </li>
                <li>
                    <b>pqcms</b> - dane systemu PQCMS
                    <ul>
                        <li><b>HOST_DB</b> - host bazy danych (np. "localhost", "poleq.pl")</li>
                        <li><b>DOMENA</b> - Twoja domena (np. "poleq.pl")</li>
                        <li><b>LOGIN</b> - login otrzymany od administratora PQCMS</li>
                        <li><b>KLUCZ_LICENCYJNY</b> - klucz licencyjny otrzymany od administratora PQCMS</li>
                    </ul>
                </li>

            </ul>

            Jeśli wprowadzone dane są prawidłowe, powracając do aplikacji powinniśmy zobaczyć formularz niezawierający
            żadnego błędu:
            <i>Jeśli błąd dalej występuje, skontaktuj się z administratorem PQCMS!</i>

            <img src="images/after_json_changes.png" alt="Panel po zmianach data.json"/>
        </section>
    </div>
</body>
</html>