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

    <title>Poradnik - Ustawienia, PQCMS | PQCMS</title>
    <meta name="description" content="Oficjalna strona systemu PQCMS">
    <meta name="keywords" content="">
    <meta name="author" content='Wiktor "PoLeq" Soliński'>
    <meta http-equiv="X-Ua-Compatible" content="IE=edge">
    <link rel="icon" type="image/x-icon" href="../../../../../images/PQCMS.svg">

    <link rel="stylesheet" href="../../../../../bs5/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../homepage.css">
    <link rel="stylesheet" href="../../tutorial.css">

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
        <h1>PQCMS</h1>
        Tutaj znajdziesz informacje o ustawieniach systemowych.
        <section>
            <div>
                W ustawieniach można dostrzec te odnoszące się do Systemu PQCMS.
            </div>
            <img src="images/panel.png" alt="Wygląd w panelu"/>
        </section>
        <section>
            <div>
                <b>Ilość logowań na dobę (z 1 IP)</b> - <i>(w uproszczeniu)</i> oznacza ilość prób, ile może wykonać dziennie urządzenie.
                Im wartość jest mniejsza, tym bezpieczniej.
            </div>
            <b>Zakres wartości: 1-255</b>
            <div>
                <b>Sesja użytkownika</b> - <i>(jak napisane wyżej)</i> oznacza, przez jaki czas (w sekundach) sesja logowania będzie przechowywana
                na serwerach PQCMS. Jeśli ten czas upłynie, pomimo tego, że użytkownik może mieć dalej aktywną sesję na TWOIM serwerze,
                to i tak nie będzie miał dostępu do zmiany danych (czy do ich wglądu).
            </div>
            <b>Zakres wartości: 60-3600</b>
        </section>
        <section>
            Jeśli zmienisz ustawienie, a później chcesz przywrócić jej wartość domyślną, wystarczy zaznaczyć przycisk
            "reset" pod danym polem. Wtedy niezależnie od wpisanej wartości, zostanie ona zresetowana.
        </section>
    </div>
</body>
</html>