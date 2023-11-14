<?php

    require "WalletData.php";
    $wallet = new WalletData();

    require "dataClasses/Transaction.php";

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfel - ElectroCMS</title>

    <link rel="stylesheet" href="../../bs5/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../default.css">
    <link rel="stylesheet" href="style.css">

    <script src="../../bs5/js/bootstrap.min.js" async></script>
</head>
<body>
    <div class="col-12 p-2 bg-info d-flex">
        <div class="col-8 bg-danger d-flex flex-column">
            <div class="display-1 text-center">
                <span style="border-bottom: 1px solid black;">
                    <?php
                        echo Transaction::getTransactionMoney();
                    ?>
                    PLN
                </span>
                <span class="h4">
                    Z planami:
                </span>
            </div>
            <div class="mt-5 p-3">
                <header class="h3 text-uppercase">
                    Historia
                </header>

                <ul class="d-flex list-unstyled p-2 transaction-history-element" style="background-color: rgba(0,0,0,0); color: white">
                    <li class="col-1">ID</li>
                    <li class="col-2">TYTUŁ</li>
                    <li class="col-4">OPIS</li>
                    <li class="col-2">DATA</li>
                    <li class="col-1">GODZINA</li>
                    <li class="col-1">WARTOŚĆ</li>
                    <li class="col-2">OSOBA</li>
                </ul>

                <div class="transaction-history">
                    <?php
                        
                        foreach(array_reverse(Transaction::getTransactions()) as $transaction) {
                            if($transaction->getValue() > 0)
                                echo '<ul class="d-flex list-unstyled p-2 transaction-history-element transaction-plus">';
                            else if($transaction->getValue() < 0)
                                echo '<ul class="d-flex list-unstyled p-2 transaction-history-element transaction-minus">';
                            else
                                echo '<ul class="d-flex list-unstyled p-2 transaction-history-element">';

                            echo <<<END
                                    <li class="col-1">{$transaction->getId()}</li>
                                    <li class="col-2">{$transaction->getTitle()}</li>
                                    <li class="col-4">{$transaction->getDescription()}</li>
                                    <li class="col-2">{$transaction->getDate()}</li>
                                    <li class="col-1">{$transaction->getTime()}</li>
                                    <li class="col-1">{$transaction->getValue()}</li>
                                    <li class="col-2">{$transaction->getTargetId()}</li>
                                </ul>
                            END;
                        }
                    ?>

                    <form method="post" action="forms/AddTransactionHistory.php" id="transaction-history-form">
                        <ul class="d-flex list-unstyled p-2 transaction-history-element align-items-center" id="transaction-form-element">
                            <li class="col-2"><input type="text" name="title" placeholder="tytuł"></li>
                            <li class="col-3"><input type="text" name="description" placeholder="opis"></li>
                            <li class="col-2"><input type="text" name="date" placeholder="data"></li>
                            <li class="col-1"><input type="text" name="time" placeholder="czas"></li>
                            <li class="col-1"><input type="text" name="value" placeholder="wartość"></li>
                            <li class="col-1"><input type="text" name="targetId" placeholder="{targetId}"></li>
                            <li class="col-1">
                                <select name="targetType">
                                    <option value="">Nie dotyczy</option>
                                    <option value="c">Klient</option>
                                    <option value="e">Pracownik</option>
                                </select>
                            </li>
                            <li class="col-1" id="transaction-history-form-controls">
                                <div class="d-flex flex-column">
                                    <input type="submit" value="Dodaj">
                                    <input type="reset" value="Resetuj">
                                </div>
                            </li>
                        </ul>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-4 bg-dark d-inline-flex">
            <div class="planned-transactions p-3">
                <header class="h3 text-uppercase">
                    Planowane przychody/wydatki
                </header>
                <div class="planned-element">
                    <ul class="d-flex list-unstyled">
                        <li>ID</li>
                        <li>Tytuł</li>
                        <li>Opis</li>
                        <li>Data</li>
                        <li>Wartość (+ - zielony, - - czerwony, 0 - biały)</li>
                        <li>Od/do kogo kasa?</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 p-2 bg-warning d-flex justify-content-evenly">
        <div class="bg-dark d-flex flex-column p-3 col-5">
            <header class="col-12 text-center h2">
                Pracownicy
            </header>
            <table>
                <thead></thead>
                <tbody>
                    <tr>
                        <td>ID</td>
                        <td>Imię</td>
                        <td>Nazwisko</td>
                        <td>Reputacja</td>
                        <td><abbr title="<p><b>Ogólnie:</b> Super pracownik, na prawdę przykłada się do rozwoju ElectroCMS</p><p>**[-]** Zjadł cukierka po kryjomu, aby nikogo nie częstować</p>" class="initialism">Opis</abbr></td>
                        <td>Godziny</td>
                        <td>Wynagrodzenie</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="bg-light d-flex flex-column p-3 col-5" style="color: black">
            <header class="col-12 text-center h2">
                Klienci
            </header>
            <table>
                <thead></thead>
                <tbody>
                    <tr>
                        <td>ID</td>
                        <td>Imię</td>
                        <td>Nazwisko</td>
                        <td>Reputacja</td>
                        <td>Opis</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>