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

    <title>Poradnik - HR, Użytkownicy | PQCMS</title>
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
        Tutaj znajdziesz informacje o użytkownikach w panelu PQCMS.
        <section>
            Wchodząc w dział HR możemy zobaczyć stronę podzieloną na 3 części: użytkowników, rangi oraz uprawnienia.
            Jeśli jesteś tam pierwszy raz, prawdopodobnie zobaczysz taki komunikat z pustą tabelą.
            Gdy jesteś administratorem, nie musisz się przejmować, ponieważ masz uprawnienia do wszystkiego,
            włącznie z wyświetlaniem wszystkich użytkowników w panelu. Może się zdarzyć, że użytkownik X nie ma
            uprawnień do wyświetlania użytkowników, a wtedy mimo tego, że jest np. 5 przypisanych do strony,
            X nie będzie mógł ich wyświetlić.
            <img src="images/empty_table.png" alt="Wygląd w panelu"/>
        </section>
        <section>
            Aby dodać użytkownika, wystarczy kliknąć na czarny przycisk "+":
            <img src="images/add_user_overlay.png" alt="Nakładka z formularzem dodająca użytkownika"/>
            Wyświetli nam się taka nakładka. Poniżej opisane są pola
            <ul class="mt-3">
                <li>
                    <b>Login</b> - Identyfikuje użytkownika, jedna z dwóch pól wymaganych do logowania.
                    <u>Napis składający się tylko z małych liter a-z, bez polskich znaków (długość: 5-30)!</u>
                </li>
                <li>
                    <b>Nick</b> - Wyświetlana nazwa użytkownika.
                    <u>Napis o długości (2-30 znaków)</u>
                </li>
                <li>
                    <b>E-mail</b> - E-mail użytkownika. Nie jest wymagany, lecz zalecany.
                </li>
                <li>
                    <b>Hasło</b> - Napis o długości 8-50 znaków
                </li>
                <li>
                    <b>"Po włączeniu konto"</b> - Jeśli wyłączone, jego stan jest wyłączany. Oznacza to, że nie będzie
                    można zalogować się na to konto po jego dodaniu.
                </li>
            </ul>
            <div>
                Oprócz tego dodatkowo możesz ustawić uprawnienia nowemu użytkownikowi
                <b>(jeżeli jesteś użytkownikiem i nie masz dostępu do danego uprawnienia, nie będziesz w stanie dodać go nowemu użytkownikowi)</b>:
            </div>
            <img src="images/add_user_overlay_perms.png" alt="Nakładka z formularzem dodająca użytkownika - uprawnienia"/>
            <div>Więcej informacji o uprawnieniach znajdziesz <a href="../permissions/">w tym poradniku</a></div>
            Na samym końcu znajdziesz przycisk "Dodaj", który zatwierdza dane oraz dodaje nowego użytkownika
            <img src="images/add_user_overlay_add_button.png" alt="Nakładka z formularzem dodająca użytkownika - przycisk 'dodaj'"/>
        </section>
        <section>
            Po dodaniu użytkownika otrzymasz powiadomienie o sukcesie (lub niepowodzeniu):
            <img src="images/added_notification.png" alt="Powiadomienie z dodanym użytkownikiem"/>
            Dodatkowo użytkownik zostanie dodany do tabeli:
            <img src="images/added_table.png" alt="Tabela z użytkownikami"/>
        </section>
        <section>
            Gdy masz możliwość wyświetlenia co najmniej 1 użytkownika, nad tabelą zostaną dodane informacje opisujące
            dane kolumny (jeśli ich nie ma, odśwież stronę)
            <img src="images/filled_table.png" alt="Tabela z użytkownikami - uzupełniona"/>
        </section>
        <section>
            Klikając na użytkownika (nie na komórkę pod kolumną "Usuń" lub "Sesja" [gdy ta ma wartość "Wł" i jest zielona])
            otwiera się nakładka podobna do tej, gdzie można było dodawać użytkownika. Możesz tutaj zmienić wszystko,
            (nawet hasło!) oprócz loginu, który jest identyfikatorem użytkownika
            <img src="images/edit_user_overlay.png" alt="Nakładka edytora użytkownika"/>
            Jeśli chcesz tylko ZRESETOWAĆ (nie ustawić) hasło użytkownika, wystarczy zjechać na sam dół nakładki:
            <img src="images/reset_password.png" alt="Reset hasła"/>
            <div><b>UWAGA!</b> Możesz tylko zresetować hasło, gdy użytkownik ma przypisany adres e-mail!</div>
            <img src="images/reset_password_error.png" alt="Reset hasła: błąd"/>
            W przypadku poprawnego resetu:
            <img src="images/reset_password_success.png" alt="Reset hasła: e-mail"/>
            Użytkownik dodatkowo otrzyma e-mail z linkiem, który umożliwia wpisanie nowego hasła
            <img src="images/reset_password_email.png" alt="Reset hasła: e-mail"/>
            <b>
                Warto dodać, że oficjalna strona PQCMS aktualnie korzysta z domeny "poleq.pl" - warto ją zapamiętać i zawsze
                sprawdzać przy podawaniu poufnych danych. Pamiętaj również o tym, że administrator PQCMS nigdy nie zapyta
                Cię o dane logowania!
            </b>
            <div class="my-3">
                Po kliknięciu na link trzeba wpisać nowe hasło oraz je powtórzyć. Jeśli hasła różnią się, użytkownik musi
                ponownie je wpisać. Gdy hasła są identyczne, hasło zostaje zmienione. Jeśli wyświetla się komunikat
                "Niepoprawny token", wtedy są 2 przyczyny: użytkownik zmienił już hasło danym linkiem (jest on jednorazowy),
                lub jeszcze go nie zmienił, lecz minęło 30 minut od wysłania wiadomości e-mail.
            </div>
            <b style="color: red">
                UWAGA! Nie można resetować hasła administratorom jak zwykłym użytkownikom. Jeśli zapomnisz hasła, musisz
                telefonicznie skontaktować się z właścicielem systemu PQCMS.
            </b>
        </section>
        <section>
            Jeżeli użytkownik jest zalogowany, możesz zobaczyć jego sesję najeżdżając na komórkę pod kolumną "Sesja".
            Wtedy ukaże się małe okienko z napisem: "Sesja wygasa: RRRR-MM-DD GG:MM:SS" - oczywiście z odpowiednią datą.
            <img src="images/session_on.png" alt="Włączona sesja użytkownika"/>
            Klikając na tę komórkę, unieważniasz sesję użytkownikowi:
            <img src="images/invalidate_session_notification.png" alt="Wyłączona sesja użytkownika - powiadomienie, panel (admin)"/>
            A użytkownikowi pokaże się następujący komunikat:
            <img src="images/invalidate_session.png" alt="Wyłączona sesja użytkownika - powiadomienie, aplikacja (użytkownik)"/>
        </section>
        <section>
            Klikając na komórkę "Usuń" możesz usunąć użytkownika:
            <img src="images/delete_user_notification.png" alt="Usuwanie użytkownika"/>
        </section>
    </div>
</body>
</html>