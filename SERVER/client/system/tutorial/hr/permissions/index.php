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

    <title>Poradnik - HR, Uprawnienia | PQCMS</title>
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
    <div id="site-container" class="col-12 p-5">
        <h1>PQCMS</h1>
        Tutaj znajdziesz informacje o rangach w panelu PQCMS.
        <section>
            Uprawnienia to jeden z kluczowych systemów PQCMS. Są one bardzo ważne, ponieważ dodając osobę do naszej strony
            nie zawsze chcemy, aby miała ona uprawnienia do wszystkiego. PQCMS umożliwia ich ograniczenie, aby można było
            bezpiecznie korzystać z systemu.
        </section>
        <section>
            Listę wszystkich uprawnień możemy zobaczyć w panelu PQCMS, pod zakładką "HR". Znajduje się tam jako 3. kolumna:
            <img src="images/permissions_table.png" alt="Tabela z rangami"/>
            Kolumna "uprawnienie" zawiera "prawidłową nazwę" - czyli taką, jaka faktycznie jest wykorzystywana do sprawdzania
            uprawnień. Nie musisz na nią zbytnio zwracać uwagi, ale może ona posłużyć jako pomoc w trakcie ustawiania
            uprawnień użytkownikom/rangom.
        </section>
        <section>
            Uprawnienia nie są takie trudne, jak może się wydawać! Tak naprawdę trudniej jest je wytłumaczyć, niż zrozumieć.
            Potrzeba tylko praktyki, aby zorientować się o co chodzi.
        </section>
        <section>
            <div>
                Ważną informacją jest to, że uprawnienia są sprawdzane od końca (patrz na kolumnę uprawnienie) w tabeli na
                zdjęciu wyżej.
            </div>
            <div>
                Przykładowo, jeżeli sprawdzana jest wartość uprawnienia <div class="pre">pqcms.hr.rank.edit.displayname</div>
                (Zmiana wyświetlanej nazwy rangom) - odnosi się do <b>wszystkich</b> rang, to kolejność jest następująca:
                <ul>
                    <li class="my-1"><div class="pre">pqcms.hr.rank.edit.displayname</div> - Zmiana wyświetlanej nazwy rangom</li>
                    <li class="my-1"><div class="pre">pqcms.hr.rank.edit</div> - Pełna edycja rang </li>
                    <li class="my-1"><div class="pre">pqcms.hr.rank</div> - Wszystko o rangach</li>
                    <li class="my-1"><div class="pre">pqcms.hr</div> - Wszystko w dziale HR</li>
                    <li class="my-1"><div class="pre">pqcms</div> - Wszystko w pakiecie PQCMS</li>
                    <li class="my-1"><div class="pre">*</div> - Wszystko</li>
                </ul>
                W tej kolejności sprawdzane jest:

            </div>
        </section>
        <section id="perm_order">
            Kolejnym ważnym aspektem jest kolejność sprawdzania <b>uprawnienia</b>
            <ul class="mt-4">
                <li>Czy użytkownik ma uprawnienia</li>
                <li>Czy ranga o najwyższym priorytecie ma uprawnienia (o ile jakąś ma)</li>
                <li>Czy ranga o niższym priorytecie ma uprawnienia... (przeszukiwane są wszystkie rangi)</li>
<!--                <li>Wartość domyślna uprawnienia</li>-->
            </ul>
            Jeżeli na tym etapie wartość jest ustawiona na wartość prawda/fałsz, zostanie ona przyjęta jako wartość ostateczna
            (reszta nie będzie wchodzić w grę)
        </section>
        <section>
            <div>
                Poniżej przedstawiony jest przykład (uprawnienia ustawione na wartość mają kolor: <span style="color: red">"nie"</span> ; <span style="color: green">"tak"</span> ):
            </div>
            <div class="my-4">
                (Użytkownik) pracownik
                <ul class="d-flex flex-column gap-1">
                    <li class="pre" style="color: red">*</li>
                    <li class="pre" style="color: green">pqcms.hr.rank.edit.displayname</li>
                    <li class="pre" style="color: red">pqcms.hr.rank.edit.priority</li>
                </ul>
            </div>
            <div class="my-4">
                (Ranga) zarzadca
                <ul class="d-flex flex-column gap-1">
                    <li class="pre" style="color: green">pqcms.hr.rank.edit</li>
                    <li class="pre" style="color: red">pqcms.hr.rank.edit.displayname</li>
                </ul>
            </div>
            Użytkownik chce edytować rangę, w takim razie sprawdzane będą następujące uprawnienia:
            <ul class="d-flex flex-column gap-1">
                <li class="pre">pqcms.hr.rank.edit.displayname</li>
                <li class="pre">pqcms.hr.rank.edit.priority</li>
                <li class="pre">pqcms.hr.rank.edit.perms</li>
            </ul>

            Sprawdzanie odbywa się w następujący sposób:
            <div class="my-4"></div>

            <div>• <div class="pre">pqcms.hr.rank.edit.displayname</div></div>
            <span style="color: green">Użytkownik ma uprawnienia!</span>
            <div>
                Możesz zauważyć, że ranga "zarzadca" ma to uprawnienie ustawione na wartość <span style="color: red">"nie"</span>.<br/>
                Nie ma to jednak znaczenia, ponieważ taka jest <a href="#perm_order">kolejność sprawdzania uprawnienia</a>
            </div>


            <div class="my-2"></div>


            <div>• <div class="pre">pqcms.hr.rank.edit.perms</div></div>
            <span style="color: grey">Użytkownik nie ma nadanych uprawnień.</span>
            <span style="color: grey">Ranga "zarzadca" nie ma nadanych uprawnień.</span>
            <div>
                W tym momencie następuje "wejście 1 szczebel wyżej": (sprawdzane jest uprawnienie <div class="pre">pqcms.hr.rank.edit</div>)
            </div>
            <span style="color: grey">Użytkownik nie ma nadanych uprawnień.</span>
            <span style="color: green">Ranga "zarzadca" ma uprawnienia!</span>


            <div class="my-2"></div>


            <div>• <div class="pre">pqcms.hr.rank.edit.priority</div></div>
            <span style="color: red">Użytkownik nie ma uprawnień!</span>

        </section>
    </div>
</body>
</html>