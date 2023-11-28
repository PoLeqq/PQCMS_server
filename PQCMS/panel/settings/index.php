<!-- <form method="post" action="updateData.php">
    Domena: <input name="domain" placeholder="domena"> <input type="checkbox" name="domainNull">
    <input type="submit">
</form> -->

<?php

session_start();
if(empty($_SESSION["pqcms-panel-username"]))
{
    header("location: ../");
    die("Najpierw musisz się zalogować! Błędne przekierowanie.");
}

require_once(dirname(__DIR__, 2) . "/config/data/JSONDatabase.php");
require_once(dirname(__DIR__, 2) . "/config/data/JSONPQCMS.php");

$databaseData = new JSONDatabase();
$pqcms = new JSONPQCMS();

//    TODO zrobić, aby te dane pobierały się z Communicatora (czas tokenu)
$tokenExpireTime = 600;
$_SESSION["pqcms-panel-settings-system-token"] = bin2hex(random_bytes(64));
$_SESSION["pqcms-panel-settings-system-token-expire"] = time() + $tokenExpireTime;

$_SESSION["pqcms-panel-settings-database-token"] = bin2hex(random_bytes(64));
$_SESSION["pqcms-panel-settings-database-token-expire"] = time() + $tokenExpireTime;

$_SESSION["pqcms-panel-settings-settings-token"] = bin2hex(random_bytes(64));
$_SESSION["pqcms-panel-settings-settings-token-expire"] = time() + $tokenExpireTime;

//    Pobieranie ustawień z serwera
require_once(dirname(__DIR__,2)."/Communicator.inc.php");
$websiteSettingsResponse = Communicator::communicate(CommunicateURL::GET_SETTINGS,[]);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="../../../bs5/css/bootstrap.min.css">
    <link rel="stylesheet" href="../default.css">
</head>
<body>
    <div class="p-4">
        <div class="row col-12">
            <form method="POST" action="updateSystem.php" class="col-3 p-4">
                <fieldset class="d-flex flex-column justify-content-center align-items-start" >
                    <legend class="">PQCMS</legend>

                    <input type="hidden" name="token" value="<?php echo $_SESSION["pqcms-panel-settings-system-token"] ?>">

                    <label class="mt-1">Użytkownik</label>
                    <input type="text" name="user" class="my-2 rounded-0" placeholder="użytkownik" value="<?php echo $pqcms->getLogin() ?>" />
                    
                    <label class="mt-1">Klucz licencyjny</label>
                    <input type="text" name="license_key" class="my-2 rounded-0" placeholder="klucz" value="<?php echo $pqcms->getLicenseKey() ?>" />

                    <input type="submit" name="submit" class="btn btn-primary my-4 rounded-0" value="Aktualizuj">

                </fieldset>
                <?php
                if(isset($_SESSION["pqcms-panel-settings-system-suc"]) && isset($_SESSION["pqcms-panel-settings-system-desc"]))
                {
                    $classResult = ($_SESSION["pqcms-panel-settings-system-suc"]) ? "success" : "error";

                    echo<<<END
                    <span class="{$classResult}">
                        {$_SESSION["pqcms-panel-settings-system-desc"]}
                    </span>
                    END;
                    unset($_SESSION["pqcms-panel-settings-system-suc"]);
                    unset($_SESSION["pqcms-panel-settings-system-desc"]);
                }
                if(!empty($_SESSION["pqcms-panel-settings-system-warn"]))
                {
                    echo<<<END
                    <div class="warning">
                        {$_SESSION["pqcms-panel-settings-system-warn"]}
                    </div>
                    END;
                    unset($_SESSION["pqcms-panel-settings-system-warn"]);
                }
                ?>
            </form>
            <form method="POST" action="updateDatabase.php" class="col-3 p-4"> 
                <fieldset class="d-flex flex-column justify-content-center align-items-start" >
                    <legend class="">Baza Danych</legend>

                    <input type="hidden" name="token" value="<?php echo $_SESSION["pqcms-panel-settings-database-token"] ?>">

                    <label class="mt-1">Host</label>
                    <input type="text" name="host" class="my-2 rounded-0" placeholder="nazwa hosta" value="<?php echo $databaseData->getHost() ?>" />
                    
                    <label class="mt-1">Użytkownik</label>
                    <input type="text" name="user" class="my-2 rounded-0" placeholder="nazwa użytkownika" value="<?php echo $databaseData->getUser() ?>" />
                    
                    <label class="mt-1">Hasło</label>
                    <input type="text" name="password" class="my-2 rounded-0" placeholder="hasło" value="<?php echo $databaseData->getPassword() ?>" />

                    <input type="submit" name="submit" class="btn btn-primary my-4 rounded-0" value="Aktualizuj">

                </fieldset>
                <?php
                if(isset($_SESSION["pqcms-panel-settings-database-suc"]) && isset($_SESSION["pqcms-panel-settings-database-desc"]))
                {
                    $classResult = ($_SESSION["pqcms-panel-settings-database-suc"]) ? "success" : "error";

                    echo<<<END
                    <span class="{$classResult}">
                        {$_SESSION["pqcms-panel-settings-database-desc"]}
                    </span>
                    END;

                    unset($_SESSION["pqcms-panel-settings-database-suc"]);
                    unset($_SESSION["pqcms-panel-settings-database-desc"]);
                }
                if(!empty($_SESSION["pqcms-panel-settings-database-warn"]))
                {
                    echo<<<END
                        <span class="warning">
                            Ostatnia aktualizacja danych spowodowała utracenie połączenia z bazą danych!
                        </span>
                        END;
                    unset($_SESSION["pqcms-panel-settings-database-warn"]);
                }
                ?>
                <?php
                require_once(dirname(__DIR__, 2)."/utils/database/Database.inc.php");
                if(Database::getConnection() == null)
                    echo<<<END
                    <span class="error">
                        Nie można połączyć z bazą danych! Upewnij się, że dane powyżej są prawidłowe!                            
                    </span>    
                    END;
                ?>
            </form>
            <form method="POST" action="updateSettings.php" class="col-3 p-4">
                <fieldset class="d-flex flex-column justify-content-center align-items-start" >
                    <legend class="">Ustawienia</legend>

                    <?php
                    if($websiteSettingsResponse["suc"] == 0)
                        echo<<<END
                        <div class="error">
                            Brak połączenia z serwerem. Czy na pewno wpisane dane systemowe (Ustawienia -> PQCMS) są dobre?<br>
                            Opis: {$websiteSettingsResponse["desc"]}
                        </div>
                        END;
                    else
                        echo<<<END
                        <input type="hidden" name="token" value="{$_SESSION["pqcms-panel-settings-settings-token"]}">

                        <label for="login_count" class="mt-1">Ilość logowań na dobę (devcom: link?)</label>
                        <i>(devcom: minusowe wartości - nielimitowane)</i>
                        <i>(devcom: 0 - zablokowane (oprócz admina))</i>
                        <input type="number" name="login_count" id="login_count" class="my-2 rounded-0" placeholder="czas w sekundach" value="{$websiteSettingsResponse["resp"]["login_attempts"]}" />
    
                        <label>
                            <input type="checkbox" name="login_count_reset" class="my-2 rounded-0" placeholder="czas w sekundach" value="" />
                            Reset
                        </label>
    
                        <label for="token_lifespan" class="mt-1">Żywotność tokenu CSRF</label>
                        <i>Przez ile czasu token CSRF będzie ważny</i>
                        <input type="text" name="token_lifespan" id="token_lifespan" class="my-2 rounded-0" placeholder="czas w sekundach" value="{$websiteSettingsResponse["resp"]["token_lifespan"]}" />
    
                        <label>
                            <input type="checkbox" name="token_lifespan_reset" class="my-2 rounded-0" placeholder="czas w sekundach" value="" />
                            Reset
                        </label>
    
                        <input type="submit" name="submit" class="btn btn-primary my-4 rounded-0" value="Aktualizuj">
END;
                    ?>
                </fieldset>
                <?php
                if(isset($_SESSION["pqcms-panel-settings-settings-suc"]) && isset($_SESSION["pqcms-panel-settings-settings-desc"]))
                {
                    $classResult = ($_SESSION["pqcms-panel-settings-settings-suc"]) ? "success" : "error";

                    echo<<<END
                    <span class="{$classResult}">
                        {$_SESSION["pqcms-panel-settings-settings-desc"]}
                    </span>
                    END;

                    unset($_SESSION["pqcms-panel-settings-settings-suc"]);
                    unset($_SESSION["pqcms-panel-settings-settings-desc"]);
                }
                ?>
            </form>
            <div class="col-3 p-4">
                <h4>Aktualizacje</h4>
                <a href="update/">Szukaj aktualizacji</a>;
            </div>
        </div>
    </div>
</body>
</html>