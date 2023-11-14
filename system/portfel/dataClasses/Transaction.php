<?php

    include "PlannedTransaction.php";

    class Transaction extends PlannedTransaction
    {
        protected $targetId;
        protected $targetType;
        protected $time;

        public function __construct(int $id, string $title, string $description, float $value, string $date, string $time, int $targetId, string $targetType) {
            parent::__construct($id,$title,$description,$value,$date);
            $this->time = $time;
            $this->targetId = $targetId;
            $this->targetType = $targetType;
        }

        public function getTargetId() {
            return $this->targetId;
        }

        public function getTargetType() {
            return $this->targetType;
        }

        public function getTime() {
            return $this->time;
        }

        public function save() {
            require_once "../WalletData.php";
            $wallet = new WalletData();

            $transactionJSON = array(
                "id" => $this->id,
                "title" => $this->title,
                "description" => $this->description,
                "date" => $this->date,
                "time" => $this->time,
                "value" => $this->value,
                "targetType" => $this->targetType,
                "targetId" => $this->targetId,
            );

            $transactions = $wallet->getObject("transactionHistory");
            array_push($transactions,$transactionJSON);

            $wallet->setObject("transactionHistory",$transactions);
            $wallet->saveData();
        }

        public static function getTransactionById(int $id) {
            require_once "../WalletData.php";
            $wallet = new WalletData();

            foreach($wallet->getObject("transactionHistory") as $transaction) {
                if($transaction["id"] == $id)
                    return $transaction;
            }
            return null;
        }

        public static function getTransactions() {
            require_once __DIR__."/../WalletData.php";
            $wallet = new WalletData();
            $transactionsArray = [];
            foreach($wallet->getObject("transactionHistory") as $trans)
                array_push($transactionsArray,new Transaction((int) $trans["id"],$trans["title"],$trans["description"],(float) $trans["value"],$trans["date"],$trans["time"],(int) $trans["targetId"],$trans["targetType"]));
            return $transactionsArray;
        }

        public static function getTransactionMoney() {
            $money = 0;
            foreach(Transaction::getTransactions() as $trans)
                $money += $trans->getValue();
            return $money;
        }
    }