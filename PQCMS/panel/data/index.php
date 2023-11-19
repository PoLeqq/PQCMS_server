<!-- <form method="post" action="updateData.php">
    Domena: <input name="domain" placeholder="domena"> <input type="checkbox" name="domainNull">
    <input type="submit">
</form> -->

<?php

    require_once(dirname(__DIR__,2)."/config/data/JSONDatabase.php");
    require_once(dirname(__DIR__,2)."/config/data/JSONPQCMS.php");

    $databaseData = new JSONDatabase();
    $electrocms = new JSONPQCMS();

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
        <i>Jeżeli chcesz ustawić pole puste, po prostu nic nie wpisuj.</i>
        <div class="row col-12">
            <form method="POST" action="updateSystem.php" class="col-3 p-4">
                <fieldset class="d-flex flex-column justify-content-center align-items-start" >
                    <legend class="">ElectroCMS</legend>

                    <label class="mt-1">Użytkownik</label>
                    <input type="text" name="user" class="my-2 rounded-0" placeholder="użytkownik" value="<?php echo $electrocms->getLogin() ?>" />
                    
                    <label class="mt-1">Klucz licencyjny</label>
                    <input type="text" name="licenseKey" class="my-2 rounded-0" placeholder="klucz" value="<?php echo $electrocms->getLicenseKey() ?>" />

                    <input type="submit" name="submit" class="btn btn-primary my-4 rounded-0" value="Aktualizuj">

                </fieldset>
            </form>
            <form method="POST" action="updateDatabase.php" class="col-3 p-4"> 
                <fieldset class="d-flex flex-column justify-content-center align-items-start" >
                    <legend class="">Baza Danych</legend>

                    <label class="mt-1">Host</label>
                    <input type="text" name="host" class="my-2 rounded-0" placeholder="nazwa hosta" value="<?php echo $databaseData->getHost() ?>" />
                    
                    <label class="mt-1">Użytkownik</label>
                    <input type="text" name="user" class="my-2 rounded-0" placeholder="nazwa użytkownika" value="<?php echo $databaseData->getUser() ?>" />
                    
                    <label class="mt-1">Hasło</label>
                    <input type="text" name="password" class="my-2 rounded-0" placeholder="hasło" value="<?php echo $databaseData->getPassword() ?>" />

                    <input type="submit" name="submit" class="btn btn-primary my-4 rounded-0" value="Aktualizuj">

                </fieldset>
                <?php
                    require_once(dirname(__DIR__,2)."/utils/database/Database.inc.php");
                    if(Database::getConnection() == null)
                        echo<<<END
                        <span style="color: red">
                            Nie można połączyć z bazą danych! Upewnij się, że dane powyżej są prawidłowe!                            
                        </span>    
                        END;

                ?>
            </form>
        </div>
    </div>
</body>
</html>