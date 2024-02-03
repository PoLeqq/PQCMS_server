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

    <title>Poradnik - HR, Rangi | PQCMS</title>
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
        Tutaj znajdziesz informacje o rangach w panelu PQCMS.
        <section>
            Wchodząc w dział HR możemy zobaczyć stronę podzieloną na 3 części: użytkowników, rangi oraz uprawnienia.
            Jeśli jesteś tam pierwszy raz, prawdopodobnie zobaczysz taki komunikat z pustą tabelą.
            Gdy jesteś administratorem, nie musisz się przejmować, ponieważ masz uprawnienia do wszystkiego,
            włącznie z wyświetlaniem wszystkich rang w panelu. Może się zdarzyć, że użytkownik X nie ma
            uprawnień do wyświetlania rang, a wtedy mimo tego, że jest np. 5 przypisanych do strony,
            X nie będzie mógł ich wyświetlić.
            <img src="images/empty_table.png" alt="Wygląd w panelu"/>
        </section>
        <section>
            Aby dodać rangę, wystarczy kliknąć na czarny przycisk "+":
            <img src="images/add_rank_overlay.png" alt="Nakładka z formularzem dodająca rangę"/>
            Wyświetli nam się taka nakładka. Poniżej opisane są pola
            <ul class="mt-3">
                <li>
                    <b>Nazwa rangi (identyfikator)</b> - identyfikująca nazwa rangi
                    <u>Napis składający się tylko z małych liter a-z, bez polskich znaków (długość: 5-30)!</u>
                </li>
                <li>
                    <b>Nazwa wyświetlana</b> - wyświetlana nazwa rangi.
                    <u>Napis o długości (2-30 znaków)</u>
                </li>
                <li>
                    <b>Priorytet</b> - priorytet rangi. Im jest wyższy, tym ranga jest "ważniejsza". Będzie to miało
                    odzwierciedlenie podczas weryfikacji uprawnień użytkownika. <u>Numer (0-65535)</u>
                </li>
            </ul>
            <div>
                Oprócz tego dodatkowo możesz ustawić uprawnienia nowej randze
                <b>(jeżeli jesteś użytkownikiem i nie masz dostępu do danego uprawnienia, nie będziesz w stanie dodać go nowej randze)</b>:
            </div>
            <div>
                Więcej informacji o uprawnieniach znajdziesz <a href="../permissions/">w tym poradniku</a>
            </div>
            Na samym końcu znajdziesz przycisk "Dodaj", który zatwierdza dane oraz dodaje nową rangę
            <img src="images/add_rank_overlay_add_button.png" alt="Nakładka z formularzem dodająca rangę - przycisk 'dodaj'"/>
        </section>
        <section>
            Po dodaniu rangi otrzymasz powiadomienie o sukcesie (lub niepowodzeniu):
            <img src="images/added_notification.png" alt="Powiadomienie z dodaną rangą"/>
            Dodatkowo ranga zostanie dodany do tabeli:
            <img src="images/added_table.png" alt="Tabela z rangami"/>
        </section>
        <section>
            Gdy masz możliwość wyświetlenia co najmniej 1 rangę, nad tabelą zostaną dodane informacje opisujące
            dane kolumny (jeśli ich nie ma, odśwież stronę)
            <img src="images/filled_table.png" alt="Tabela z rangami - uzupełniona"/>
        </section>
        <section>
            Klikając na rangę (nie na komórkę pod kolumną "Usuń")
            otwiera się nakładka podobna do tej, gdzie można było dodawać rangę. Możesz tutaj zmienić wszystko,
            oprócz ID, które jest identyfikatorem rangi
            <img src="images/edit_rank_overlay.png" alt="Nakładka edytora rangi"/>
            Na samym dole wystarczy kliknąć przycisk "Edytuj"
        </section>
        <section>
            Jeżeli mamy już rangi w panelu, tworząc nowego użytkownika możemy nadać mu rangę zaznaczając wartość
            uprawnienia na "tak" (na zielono):
            <img src="images/rank_permission.png" alt="Uprawnienie rangi dla użytkownika"/>
        </section>
        <section>
            Klikając na komórkę "Usuń" możesz usunąć rangę:
            <img src="images/delete_rank_notification.png" alt="Usuwanie rangi"/>
        </section>
    </div>
</body>
</html>