<?php
session_start();
if(empty($_SESSION["pqcms-panel-username"]))
{
    header("location: ../");
    die("Najpierw musisz się zalogować! Błędne przekierowanie.");
}
?>
<ul>
    <li>Strona (tekst)
        <!-- <pre>User;ID;Nazwa;(grupa);Stary Tekst;Nowy Tekst</pre> -->
    </li>
    <li>
        HR:
        <ul>
            <li>
                Grupa
                <!-- <pre>User;ID;Nazwa;</pre> -->
            </li>
            <li>Użytkownicy</li>
        </ul>
    </li>
    <li>
        Data (data.json, settings.json [zmiany]):
    </li>
</ul>
