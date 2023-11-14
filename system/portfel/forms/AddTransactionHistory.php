<?php

    require "../dataClasses/Transaction.php";
    
    $transactions = Transaction::getTransactions();
    $transactionId = $transactions[count($transactions)-1]->getId()+1;
    $transaction = new Transaction((int) $transactionId, $_POST["title"], $_POST["description"],
        (float) $_POST["value"], $_POST["date"], $_POST["time"], (int) $_POST["targetId"], $_POST["targetType"]);

    $transaction->save();

    header("location: ../");