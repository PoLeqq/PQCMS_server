<!-- <form method="post" action="updateData.php">
    Domena: <input name="domain" placeholder="domena"> <input type="checkbox" name="domainNull">
    <input type="submit">
</form> -->

<?php

    require_once("../../config/JSONDatabase.php");
    require_once("../../config/JSONPQCMS.php");

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
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <div class="p-4">
        <i>Jeżeli chcesz ustawić pole na puste, po prostu nic nie wpisuj.</i>
        <div class="row col-12">
            <form method="POST" action="updateElectroCMS.php" class="col-3 p-4"> 
                <fieldset class="d-flex flex-column justify-content-center align-items-start" >
                    <legend class="">ElectroCMS</legend>

                    <label class="mt-1">Użytkownik</label>
                    <input type="text" name="user" class="my-2 rounded-0" placeholder="użytkownik" value="<?php echo $electrocms->getUser() ?>" />
                    
                    <label class="mt-1">Klucz licencyjny</label>
                    <input type="text" name="licenseKey" class="my-2 rounded-0" placeholder="klucz" value="<?php echo $electrocms->getLicenseKey() ?>" />

                    <input type="submit" name="submit" class="btn btn-primary my-4 rounded-0" value="Aktualizuj">

                </fieldset>
            </form>
            <form method="POST" action="updateDatabase.php" class="col-3 p-4"> 
                <fieldset class="d-flex flex-column justify-content-center align-items-start" >
                    <legend class="">Baza Danych</legend>

                    <label for="form_db_host" class="mt-1">Host</label>
                    <input id="form_db_host" type="text" name="host" class="my-2 rounded-0" placeholder="nazwa hosta" value="<?php echo $databaseData->getHost() ?>" />
                    
                    <label for="form_db_user" class="mt-1">Użytkownik</label>
                    <input id="form_db_user" type="text" name="user" class="my-2 rounded-0" placeholder="nazwa użytkownika" value="<?php echo $databaseData->getUser() ?>" />
                    
                    <label for="form_db_password" class="mt-1">Hasło</label>
                    <input id="form_db_password" type="text" name="password" class="my-2 rounded-0" placeholder="hasło" value="<?php echo $databaseData->getPassword() ?>" />

                    <input type="submit" name="submit" class="btn btn-primary my-4 rounded-0" value="Aktualizuj">
                </fieldset>
            </form>
        </div>
    </div>
</body>
</html>