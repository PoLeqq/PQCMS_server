<?php

class Database
{
    /**
     * Tworzy połączenie z bazą danych, zwraca obiekt w formie mysqli (lub null, gdy połączenie nie powiodło się)
     */
    public static function getConnection(): bool|mysqli|null
    {
        require_once(dirname(__DIR__,2) . "/config/data/JSONDatabase.php");

        $db = new JSONDatabase();
        try{
            $connect = mysqli_connect($db->getHost(), $db->getUser(), $db->getPassword());
        } catch(Exception) {
            return null;
        }

        if (mysqli_errno($connect) != 0)
            return null;

        // Stworzenie bazy danych, jeżeli nie istnieje
        $query = 'CREATE DATABASE IF NOT EXISTS ' . $db->getName();
        mysqli_query($connect, $query);

        // Ustawienie bazy danych na poprawną
        mysqli_select_db($connect, $db->getName());

        return $connect;
    }

    /**
     * Funkcja dodająca wiersz do tabeli
     */
    function insertInto($conn, $table, ...$colsAndVales): void
    {
        $size = count($colsAndVales) / 2;
        $cols = array_slice($colsAndVales, 0, $size);
        $values = array_slice($colsAndVales, $size);

        $params = str_repeat("?,", $size);
        $params = rtrim($params, ",");

        $stringCols = implode(",", $cols);

        $valTypes = "";
        for ($i = 0; $i < $size; $i++) {
            switch (gettype($values[$i])) {
                case "boolean":
                    $values[$i] = intval($values[$i]);
//                    Linijki niżej nie było, dodałem i nie ma błędu, ale czy na pewno git?
                    $valTypes .= "b";
//                    Break dodany później, ale chyba git???
                    break;
                case "integer":
                    $valTypes .= "i";
                    break;
                case "double":
                    $valTypes .= "d";
                    break;
                default:
                    $valTypes .= "s";
                    break;
            }
        }

        $sql = "INSERT INTO $table ($stringCols) VALUES ($params)";
        $query = $conn->prepare($sql);
        $query->bind_param($valTypes, ...$values);
        $query->execute();
    }

    /**
     * Funkcja usuwająca wiersz z danej tabeli $table gdzie pole $col jest równe wartości $val
     */
    function deleteRowWhere($conn, $table, $col, $val): void
    {
        $sql = "DELETE FROM $table WHERE $col = ?";
        $query = $conn->prepare($sql);
        $query->bind_param("s", $val);
        $query->execute();
    }

    /**
     * Funkcja pobiera wszystkie pliki .sql dołączone do utils/database/tables i je wykonuje.
     * @return array poprawność wykonania operacji z plików sql
     */
    public static function setupDefaultDatabase(): array
    {
        $conn = Database::getConnection();
        if(!$conn) return [];

        $resp = [];

        $path = __DIR__."/tables/";
        $files = scandir($path);
        foreach ($files as $file) {
            if(str_ends_with($file, '.sql')) {
                $query = $conn->query("SHOW TABLES");
                while($row = $query->fetch_row())
                    if($row[0].".sql" == $file) {
                        $resp[$file] = 0;
                        continue 2;
                    }

                $sqlFile = file_get_contents($path . $file);
                if (!$conn->multi_query($sqlFile))
                {
                    $resp[$file] = -1;
                    continue;
                }

                do if($result = $conn->store_result()) $result->free_result();
                while ($conn->next_result());

                $resp[$file] = 1;
            }
        }

        $conn->close();
        return $resp;
    }

    /**
     * Funkcja sprawdza, czy słowo kończy się danym ciągiem
     */
    function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if (!$length)
            return true;
        return substr($haystack, -$length) === $needle;
    }
}