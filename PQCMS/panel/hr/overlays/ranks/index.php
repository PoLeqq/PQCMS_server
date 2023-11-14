<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranks - ElectroCMS Overlay</title>
    
    <link rel="stylesheet" href="../../../../../bs5/css/bootstrap.min.css">
    <link rel="stylesheet" href="../overlay.css">
    <link rel="stylesheet" href="index.css">
</head>
<body>
    

    <form method="POST" action="addRank.php" class="col-12 p-4"> 
        <fieldset class="d-flex flex-column justify-content-center align-items-start">
            <legend class="">Ranga</legend>
    
            <label class="mt-1">Nazwa</label>
            <input type="text" name="name" class="my-2 rounded-0" placeholder="nazwa"/>
            
            <label class="mt-1">Rodzic</label>

            <select name="parent" class="my-2">
                <option value="">-</option>

                <?php
                    require_once "../../../../hr/Rank.php";
                    foreach(getAllRanksOrder("priority",false) as $rank)
                        echo "<option value=\"{$rank->getId()}\">$rank->name</option>";
                ?>
            </select>

            <label class="mt-1">Priorytet <i class="formAside">(im mniejszy, tym ranga jest "ważniejsza"; min. 0)</i></label>
            <input type="number" name="priority" placeholder="priorytet">

            <!-- Miejsce na permisje -->

            <input type="submit" name="submit" class="btn btn-primary my-4 rounded-0" value="Dodaj">
        </fieldset>
    </form>
</body>
</html>