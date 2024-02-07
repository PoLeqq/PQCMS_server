<!doctype html>
<html lang="pl-PL">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Strona główna | PQCMS</title>
    <meta name="description" content="Oficjalna strona systemu PQCMS">
    <meta name="keywords" content="">
    <meta name="author" content='Wiktor "PoLeq" Soliński'>
    <meta http-equiv="X-Ua-Compatible" content="IE=edge">
    <link rel="icon" type="image/x-icon" href="images/PQCMS.svg">

    <link rel="stylesheet" href="bs5/css/bootstrap.min.css">
    <link rel="stylesheet" href="default.css">
    <link rel="stylesheet" href="index.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400&display=swap" rel="stylesheet">

    <meta content="PQCMS" property="og:title"/>
    <meta content="Prosty a zarazem szybki i niezawodny system CMS, który jest ciągle rozwijany!" property="og:description" />
    <!-- <meta content="https://poleq.pl/" property="og:url" /> -->
    <!-- <meta content="https://poleq.pl/img/trex.jpg" property="og:image" /> -->
    <!-- <meta content="#6204dd" config-react-helmet="true" name="theme-color" /> -->

    <style>
        .tooltip {
            position: relative;
            display: inline-block;
            border-bottom: 1px dotted black;
            color: black;
            opacity: 1;
        }

        .tooltip::after {
            content: "(?)";
        }

        .tooltip .tooltiptext {
            opacity: 0;
            width: clamp(100px, 300px, 300px);
            background-color: black;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;

            transition: .3s;

            position: absolute;
            z-index: 1;
        }

        .tooltip:hover .tooltiptext {
            opacity: 1;
            transition: .1s;
        }
    </style>

</head>
<body>

<noscript>
    UWAGA! Korzystanie ze strony z wyłączonym JavaScriptem uniemożliwia korzystanie ze strony w pełni pięknej i funkcjonalnej! Rób jak uważasz!
</noscript>

<div id="site-container">

    <nav class="navbar navbar-expand-lg navbar-dark">

        <div class="container-fluid px-5">

            <a id="main-link" class="navbar-brand fs-2 link-nav" href="#">
                PQCMS
                <img src="images/PQCMS.svg" alt="logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">

                <ul class="navbar-nav mb-2 mb-lg-0 fs-4">

                    <li class="nav-item link-nav">
                        <a class="nav-link" href="#zalety">Zalety</a>
                    </li>

                    <li class="nav-item link-nav">
                        <a class="nav-link" aria-current="page" href="#projekt">O projekcie</a>
                    </li>

                    <li class="nav-item link-nav">
                        <a class="nav-link" href="#koszty">Koszty</a>
                    </li>

                    <li class="nav-item link-nav">
                        <a class="nav-link" href="#zamowienie">Zamówienie</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <div id="site">
        <div id="start" class="block-background">
            <div class="col-8 offset-3 p-5 block-foreground">
                <section>
                    <h1 class="mb-4">Poznaj PQCMS!</h1>
                    <p>
                        PQCMS to nowoczesny system CMS (Content Management System) stworzony z myślą o łatwym zarządzaniu treścią na stronach internetowych.
                        Jego głównym celem jest umożliwienie użytkownikom, w tym pracownikom, wygodnej i bezpiecznej edycji tekstów na stronie oraz zarządzanie
                        rangami i uprawnieniami dostępu. Dzięki temu nie tylko Ty, lecz ktoś z Twoich bliskich (czy pracowników)
                        będzie mógł w stanie edytować stronę bez obawy o utratę danych.
                    </p>
                    <p>
                        Program oferuje oczywiście wiele, wiele więcej...
                    </p>
                    <img src="images/panel.png" alt="panel" width="800" height="auto">
                </section>
            </div>
            <div class="wave multiple-wave">
                <img src="images/wave1.svg" alt="fala">
            </div>
        </div>

        <!--            <div class="block-background p-5" id="projekt">-->
        <!--                <div class="block-foreground col-8 offset-3 p-5">-->
        <!--                    <section>-->
        <!--                        <h1 class="mb-4">Poznaj PQCMS</h1>-->
        <!--                            <p>-->
        <!--                                PQCMS to nowoczesny system CMS (Content Management System) stworzony z myślą o łatwym zarządzaniu treścią na stronach internetowych.-->
        <!--                                Jego głównym celem jest umożliwienie użytkownikom, w tym pracownikom, wygodnej i bezpiecznej edycji tekstów na stronie oraz zarządzanie-->
        <!--                                rangami i uprawnieniami dostępu.-->
        <!--                            </p>-->
        <!--                            <p>-->
        <!--                                Jednym z głównych atutów PQCMS jest intuicyjny interfejs, który umożliwia łatwą edycję treści. Użytkownicy, którzy mają odpowiednie-->
        <!--                                uprawnienia, mogą modyfikować teksty na stronie. Dzięki prostemu interfejsowi nie jest wymagana duża wiedza techniczna, aby korzystać z systemu.-->
        <!--                            </p>-->
        <!--                            <p>-->
        <!--                                PQCMS umożliwia także dodawanie rang, coś w tylu stanowisk pracy. Można zdefiniować różne rangi, takie jak administrator, zarządca treści.-->
        <!--                                Każda ranga ma określone uprawnienia, które można dostosować do potrzeb. Na przykład, administrator ma pełny dostęp do wszystkich funkcji systemu,-->
        <!--                                zarządca treści ma uprawnienia do edycji treści.-->
        <!--                            </p>-->
        <!--                            <p>-->
        <!--                                Ponadto PQCMS umożliwia tworzenie i zarządzanie użytkownikami. Można dodawać nowych pracowników do systemu i przypisywać im odpowiednie rangi-->
        <!--                                oraz uprawnienia dostępu. Dzięki temu można precyzyjnie kontrolować, kto ma dostęp do poszczególnych treści i funkcji systemu.-->
        <!--                            </p>-->
        <!--                            <p>-->
        <!--                                Ważnym aspektem PQCMS jest również niezawodność. System ten został zaprojektowany z myślą o stabilności i wydajności.-->
        <!--                                Program wykorzystuje zaawansowane technologie, które minimalizują ryzyko awarii i zapewniają ciągłość działania.-->
        <!--                                Regularne aktualizacje i wsparcie techniczne są również dostępne, aby zapewnić optymalne działanie systemu.-->
        <!--                            </p>-->
        <!--                    </section>-->
        <!--                </div>-->
        <!--            </div>-->
        <div class="block2-background">
            <div class="block2-foreground">
                <section id="zalety" class="d-flex flex-column justify-content-center align-items-center">
                    <h1>Zalety</h1>
                    <div class="col-12 my-5">
                        <div class="row col-12 p-5">
                            <div class="col-6 d-flex justify-content-center align-items-center flex-column">
                                <h3>Prostota</h3>
                                Od użytkowników systemu nie wymaga się żadnej specjalistycznej wiedzy - panel administracyjny jest intuicyjny, a co za tym idzie - prosty w obsłudze.
                                Łatwość w zarządzaniu stroną internetową jest dla nas priotytetem.
                            </div>

                            <div class="col-6 d-flex justify-content-center align-items-center flex-column">
                                <h3>Wspierany projekt</h3>
                                System PQCMS jest na bieżąco aktualizowany. Wszelkie poprawki tworzone są z myślą
                                o wygodzie użytkowania oraz o bezpieczeństwie systemu. Program jest stworzony dla klientów,
                                a więc wysłuchujemy ich oczekiwań! W miarę możliwości staramy się wdrażać wasze pomysły,
                                aby obsługa panelu PQCMS była przyjemna oraz nie stanowiła wielkich kłopotów.
                            </div>
                        </div>
                        <div class="row col-12 px-5">
                            <div class="col-6 d-flex justify-content-center align-items-center flex-column">
                                <h3>Pomoc techniczna</h3>
                                PQCMS oferuje również wsparcie techniczne - jeżeli nie będziesz w stanie czegoś zrobić,
                                lub czegoś nie będziesz rozumieć (co jest zrozumiałe!), jesteśmy otwarci!
                                Można do nas śmiało pisać o pomoc! Postaramy się, aby problem został jak najszybciej
                                rozwiązany.
                            </div>

                            <div class="col-6 d-flex justify-content-center align-items-center flex-column">
                                <h3>Bezpieczeństwo</h3>
                                Duży nacisk stawiamy również na bezpieczeństwo programu. Cały czas trwają prace mające
                                na celu symulację pracę hakera. Gdy wyszukamy luki w programie, od razu je zabezpieczamy!
                                Jest to priorytetowa sprawa, dlatego w takim wypadku mniej ważne poprawki będą musiały
                                poczekać z wdrożeniem.
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="block-background">
            <div class="wave_reversed multiple-wave-reversed">
                <img src="images/wave2.svg" alt="fala">
            </div>
            <div class="col-8 offset-3 p-5 block-foreground">
                <div class="block-foreground">
                    <section id="projekt">
                        <h1>O projekcie</h1>
                        <article class="col-12 d-flex flex-column my-5">
                            <header>Dlaczego powstał projekt?</header>
                            <p>
                                PQCMS powstał z myślą o osobach, które nie mają specjalistycznej wiedzy informatycznej, lub
                                dla tych, którzy uznali, że ręczna zmiana tekstu na stronie nie jest zbyt efektywna.
                                Przywróciliśmy BBCode, dzięki któremu można formatować tekst w dowolnej formie! Kursywa,
                                paragrafy, listy, nagłówki, a nawet filmik YouTube! Oczywiście jest tego więcej!
                            </p>
                        </article>
                        <article class="col-12 d-flex flex-column my-5">
                            <header>Co oferuje panel?</header>
                            <p>
                                W panelu możemy znaleźć kilka zakładek, są to aktualnie:
                            </p>
                            <ul class="d-flex flex-column gap-2">
                                <li>
                                    <b>Strona</b> - zawiera stronę klienta, gdzie można edytować tekst.
                                </li>
                                <li>
                                    <b>HR</b> - jest to dział, w którym można dodawać, edytować użytkowników oraz
                                    rangi, sprawdzać uprawnienia, dostosowywać je do potrzeb.
                                </li>
                                <li>
                                    <b>Ustawienia</b> - zawiera ustawienia takie jak: dane do bazy danych, ustawienia
                                    systemowe, dane do licencji PQCMS (oraz czas jej wygaśnięcia)
                                </li>
                                <li>
                                    <b>Twoje dane</b> - dane konta, którego aktualnie używasz. Dozwolona jest tam również
                                    ich edycja (jeśli tylko masz odpowiednie uprawnienia)
                                </li>
                                <li>
                                    <b>Formularze</b> - dane z formularzy, które są na stronie klienta. Wszystkie
                                    informacje znajdziesz w jednym miejscu!
                                </li>
                            </ul>
                        </article>

                    </section>
                </div>
            </div>
            <div class="wave">
                <img src="images/wave3.svg" alt="fala">
            </div>
        </div>

        <div class="block2-background">
            <div class="block2-foreground">
                <section id="koszty" class="col-10 offset-1 my-5 pb-5">
                    <h1>Koszty</h1>
                    <h5>Pierwszy miesiąc licencji jest zawsze bezpłatny, aby nasi klienci mogli przetestować system</h5>
                    <div class="col-12 d-flex justify-content-center my-4 flex-column">
                        Do kosztów strony wlicza się:
                        <ul>
                            <li>
                                <b>~500zł - Wykonanie strony internetowej</b>
                            </li>
                            <li>
                                <b>50zł miesięcznie - Miesięczna licencja PQCMS</b> (opcjonalnie)
                            </li>
                            <li>
                                <b>
                                    ~100zł rocznie -
                                    <div class="tooltip">Domena
                                        <span class="tooltiptext">np. google.com , youtube.com</span>
                                    </div>
                                </b>
                                (w zależności od dostawcy)
                            </li>
                            <li>
                                <b>
                                    ~90-345zł rocznie -
                                    <div class="tooltip">Hosting
                                        <span class="tooltiptext">jest to serwer, który odpowiada za funkcjonowanie strony internetowej</span>
                                    </div>
                                </b>
                                (w zależności od dostawcy oraz wymagań)
                            </li>

                        </ul>
                    </div>
                    Dalej nie możesz się zdecydować, czy uwzględnić w koszta system PQCMS? Poproś administratora o
                    wersję demo!
                </section>

                <section id="zamowienie" class="col-10 offset-1 my-5 pt-5">
                    <form id="save-contact-form" method="post" action="forms/contact/SaveContact.php" class="col-10 offset-1 d-flex flex-column gap-3 my-3 p-5">
                        <h1>Zamówienie</h1>
                        Jeżeli chcesz prosić o wykonanie strony, pozostaw kontakt!

                        <label>
                            Imię
                            <input name="name" placeholder="Imię"/>
                        </label>
                        <label>
                            Nazwisko
                            <input name="surname" placeholder="Nazwisko"/>
                        </label>
                        <label>
                            Numer telefonu
                            <input name="phone" placeholder="nr tel."/>
                        </label>

                        <label>
                            PQCMS
                            <select name="pqcms">
                                <option value="1">Chcę system PQCMS</option>
                                <option value="0">Nie chcę systemu PQCMS</option>
                            </select>
                        </label>

                        <label>
                            Stan strony:
                            <select name="state">
                                <option disabled selected>(Proszę wybrać)</option>
                                <option value="1">Mam już stronę, chcę ją tylko połączyć z PQCMS</option>
                                <option value="2">Mam szablon strony, potrzebuję tylko jej wykonanie</option>
                                <option value="3">Wiem mniej więcej jak ma wyglądać strona</option>
                                <option value="4">Nie zastanawiałem(am) się, jaka będzie strona</option>
                                <option value="0">Inny (proszę opisać niżej)</option>
                            </select>
                        </label>

                        <label>
                            Szczegółowe informacje:
                            <textarea name="message"></textarea>
                        </label>

                        <input type="submit" value="Prześlij" class="mt-5"/>

                    </form>
                </section>
            </div>
        </div>
    </div>

    <footer class="d-flex justify-content-center align-items-center hei">
        PQCMS &copy Wszelkie prawa zastrzeżone
    </footer>
</div>

<script src="bs5/js/bootstrap.min.js"></script>
</body>
</html>