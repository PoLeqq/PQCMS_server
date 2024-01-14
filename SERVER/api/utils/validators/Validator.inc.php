<?php

class Validator
{
    /**
     * Funkcja do walidacji zmiennych<br>
     * <ul>
     *     <li>s — string</li>
     *     <li>b — boolean</li>
     *     <li>i — integer</li>
     * </ul>
     * <h3>string</h3>
     * <ul>
     *     <li>s - szuka napisu</li>
     *     <li>s(napis) - szuka "napis"</li>
     *     <li>s(x) - (gdzie x to liczba) szuka napisu o długości x</li>
     *     <li>s(x-y) - szuka napisu o długości od x do y znaków</li>
     * </ul>
     * <h3>boolean</h3>
     * <ul>
     *     <li>b - szuka boolean</li>
     *     <li>b(0) - szuka false</li>
     *     <li>b(1) - szuka true</li>
     * </ul>
     * <h3>integer</h3>
     * <ul>
     *     <li>i - szuka integer</li>
     *     <li>i(x) - szuka liczby x</li>
     *     <li>i(x-y) - szuka liczby o wartości od x do y</li>
     * </ul>
     *
     * Przykład:
     * <ul>
     *     <li>
     *         $values = ["12", true, 2]<br>
     *         $patterns = ["s(12)", "b(1)", "i(1-2)"]
     *     </li>
     *     <li>
     *         Return:
     *         ```
     *         {
     *              ["suc"] => 1,
     *              ["desc"] => "Walidacja przebiegła pomyślnie!"
     *         }
     *         ```
     *     </li>
     * </ul>
     * @param array $values wartości
     * @param array $patterns patterny
     * @return array rezultat, zwracany w formie:
     * ```json
     *  {
     *       "suc": s,
     *       "desc": d,
     *       "element_index": i
     *  }
     *  ```
     * gdzie:
     * <ul>
     *     <li>s — int: 0 lub 1 (1 - walidacja przebiegła pomyślnie)</li>
     *     <li>d — string: opis błędu, sukcesu</li>
     *     <li>i — int: indeks elementu, w którym zaszedł błąd. <b>UWAGA!</b> Nie występuje on w każdym returnie</li>
     * </ul>
     */
    public static function validate(array $values, array $patterns): array
    {
        if(count($values) != count($patterns))
            return ["suc" => 0, "desc" => "Liczba wartości nie zgadza się z liczbą szablonów!"];

        $patternTypes = ["s" => "string", "i" => "integer", "b" => "boolean", "d" => "double"];
        $i = 0;
        foreach ($patterns as $pat) {
            if (!in_array($pat[0], array_keys($patternTypes)))
                return ["suc" => 0, "desc" => "Nie znaleziono typu: $pat[0]"];

            $value = $values[$i];
            if(gettype($value) != $patternTypes[$pat[0]])
            {
                $respErrorTypes = true;
                $errGetType = gettype($value);

                if($pat[0] === "i") {
                    $int_value = ctype_digit($value) ? intval($value) : null;
                    if(!is_null($int_value))
                        $respErrorTypes = false;
                }
//                todo decimal

                if($respErrorTypes)
                    return ["suc" => 0, "desc" => "Wartość nie spełnia wymogu typu! (oczekiwano:${patternTypes[$pat[0]]}, dostarczono: ${errGetType}", "element_index" => $i];
            }

            if(strlen($patterns[$i]) != 1) {
                if ($pat[0] == "s") {
                    if (preg_match('/s\((\d+)-(\d+)\)/', $pat, $matches)) {
                        if ($matches[1] > $matches[2])
                            return ["suc" => 0, "desc" => "(Wzór) Pierwsza wartość jest większa od drugiej!", "element_index" => $i];
                        $valueStrlen = strlen($value);
                        if ($valueStrlen < $matches[1] || $valueStrlen > $matches[2])
                            return ["suc" => 0, "desc" => "Napis nie spełnia wymogu długości!", "element_index" => $i];
                    } else if (preg_match('/s\((\d+)\)/', $pat, $matches)) {
                        if (strlen($value) != $matches[1])
                            return ["suc" => 0, "desc" => "Napis nie spełnia wymogu długości!", "element_index" => $i];
//                            return ["suc" => 0, "desc" => "Napis nie spełnia wymogu długości! (znaleziono: ".strlen($value).", oczekiwano: ".$matches[1].")", "element_index" => $i];
                    } else if (preg_match('/s\((.*?)\)/', $pat, $matches)) {
                        if ($matches[1] != $value)
                            return ["suc" => 0, "desc" => "Napis nie jest równy wzorowi!", "element_index" => $i];
                    } else
                        return ["suc" => 0, "desc" => "(Wzór) Błędny wzór na string!", "element_index" => $i];
                } else if ($pat[0] == "b") {
                    if (!($pat == "b(1)" || $pat == "b(0)"))
                        return ["suc" => 0, "desc" => "(Wzór) Błędny wzór na boolean!", "element_index" => $i];

                    if ($pat == "b(1)" && !$value)
                        return ["suc" => 0, "desc" => "Wartość logiczna nie jest równa wzorowi!", "element_index" => $i];
                    else if ($pat == "b(0)" && $value)
                        return ["suc" => 0, "desc" => "Wartość logiczna nie jest równa wzorowi!", "element_index" => $i];
                } else if ($pat[0] == "i") {
                    if (preg_match('/i\((-?\d+)-(-?\d+)\)/', $pat, $matches))
                    {
                        $matches[1] = (int)($matches[1]);
                        $matches[2] = (int)($matches[2]);
                        if($matches[1] > $matches[2])
                            return ["suc" => 0, "desc" => "(Wzór) Pierwsza wartość jest większa od drugiej!", "element_index" => $i];
                        if($value < $matches[1] || $value > $matches[2])
                            return ["suc" => 0, "desc" => "Liczba nie spełnia wymogu zakresu!", "element_index" => $i];
                    }
                    elseif (preg_match('/i\((-?\d+)\)/', $pat, $matches))
                    {
                        if($matches[1] != $value)
                            return ["suc" => 0, "desc" => "Liczba nie jest równa wzorowi!", "element_index" => $i];
                    }
                    else
                        return ["suc" => 0, "desc" => "(Wzór) Błędny wzór na integer!", "element_index" => $i];
//                    if (preg_match('/i\((\d+)-(\d+)\)/', $pat, $matches)) {
//                        if ($matches[1] > $matches[2])
//                            return ["suc" => 0, "desc" => "(Wzór) Pierwsza wartość jest większa od drugiej!", "element_index" => $i];
//                        if ($value < $matches[1] || $value > $matches[2])
//                            return ["suc" => 0, "desc" => "Liczba nie spełnia wymogu zakresu!", "element_index" => $i];
//                    } else if (preg_match('/i\((.*?)\)/', $pat, $matches)) {
//                        if ($matches[1] != $value)
//                            return ["suc" => 0, "desc" => "Liczba nie jest równa wzorowi!", "element_index" => $i];
//                    } else
//                        return ["suc" => 0, "desc" => "(Wzór) Błędny wzór na integer!", "element_index" => $i];
                }
//                TODO decimal
            }
            $i++;
        }
        return ["suc" => 1, "desc" => "Walidacja przebiegła pomyślnie!"];
    }

    /**
     * Funkcja do walidacji zmiennych<br>
     * <ul>
     *     <li>s — string</li>
     *     <li>b — boolean</li>
     *     <li>i — integer</li>
     * </ul>
     * <h3>string</h3>
     * <ul>
     *     <li>s - szuka napisu</li>
     *     <li>s(napis) - szuka "napis"</li>
     *     <li>s(x) - (gdzie x to liczba) szuka napisu o długości x</li>
     *     <li>s(x-y) - szuka napisu o długości od x do y znaków</li>
     * </ul>
     * <h3>boolean</h3>
     * <ul>
     *     <li>b - szuka boolean</li>
     *     <li>b(0) - szuka false</li>
     *     <li>b(1) - szuka true</li>
     * </ul>
     * <h3>integer</h3>
     * <ul>
     *     <li>i - szuka integer</li>
     *     <li>i(x) - szuka liczby x</li>
     *     <li>i(x-y) - szuka liczby o wartości od x do y</li>
     * </ul>
     *
     * Przykład:
     * <ul>
     *     <li>
     *         $values = ["12", true, 2]<br>
     *         $patterns = ["s(12)", "b(1)", "i(1-2)"]
     *     </li>
     *     <li>
     *         Return:
     *         ```
     *         {
     *              ["suc"] => 1,
     *              ["desc"] => "Walidacja przebiegła pomyślnie!"
     *         }
     *         ```
     *     </li>
     * </ul>
     * @param array $values wartości
     * @param array $patterns patterny
     * @return array rezultat, zwracany w formie:
     * ```json
     *  {
     *       "suc": s,
     *       "desc": d,
     *       "element_index": i
     *  }
     *  ```
     * gdzie:
     * <ul>
     *     <li>s — int: 0 lub 1 (1 - walidacja przebiegła pomyślnie)</li>
     *     <li>d — string: opis błędu, sukcesu</li>
     *     <li>i — int: indeks elementu, w którym zaszedł błąd. <b>UWAGA!</b> Nie występuje on w każdym returnie</li>
     * </ul>
     */
    public static function validateAssoc(array $values, array $patterns): array
    {
        if(count(array_keys($values)) != count($patterns))
            return ["suc" => 0, "desc" => "Liczba wartości nie zgadza się z liczbą szablonów!"];

        $patternTypes = ["s" => "string", "i" => "integer", "b" => "boolean", "d" => "double"];
        $i = 0;
        foreach ($values as $name => $value) {
            if(is_array($value))
                return ["suc" => 0, "desc" => "Pole \"$name\" nie może być tablicą!"];
            $pat = $patterns[$i];
            if (!in_array($pat[0], array_keys($patternTypes)))
                return ["suc" => 0, "desc" => "Nie znaleziono typu: $pat[0]"];

//            var_dump($name);
//            var_dump($value);
            if(gettype($pat) != $patternTypes[$pat[0]])
            {
                $respErrorTypes = true;
                $errGetType = gettype($value);

                if($pat[0] === "i") {
                    $int_value = ctype_digit($value) ? intval($value) : null;
                    if(!is_null($int_value))
                        $respErrorTypes = false;
                }
//                todo decimal

                if($respErrorTypes)
                    return ["suc" => 0, "desc" => "Wartość nie spełnia wymogu typu! (oczekiwano:${patternTypes[$pat[0]]}, dostarczono: ${errGetType}", "element_index" => $i];
            }

            if(strlen($patterns[$i]) != 1) {
                if ($pat[0] == "s") {
                    if (preg_match('/s\((\d+)-(\d+)\)/', $pat, $matches)) {
                        if ($matches[1] > $matches[2])
                            return ["suc" => 0, "desc" => "(Wzór) Pierwsza wartość jest większa od drugiej!", "element_name" => $name];
                        $valueStrlen = strlen($value);
                        if ($valueStrlen < $matches[1] || $valueStrlen > $matches[2])
                            return ["suc" => 0, "desc" => "Pole \"$name\" nie spełnia wymogu długości!"];
                    } else if (preg_match('/s\((\d+)\)/', $pat, $matches)) {
                        if (strlen($value) != $matches[1])
                            return ["suc" => 0, "desc" => "Pole \"$name\" nie spełnia wymogu długości!"];
//                            return ["suc" => 0, "desc" => "Napis nie spełnia wymogu długości! (znaleziono: ".strlen($value).", oczekiwano: ".$matches[1].")", "element_name" => $name];
                    } else if (preg_match('/s\((.*?)\)/', $pat, $matches)) {
                        if ($matches[1] != $value)
                            return ["suc" => 0, "desc" => "Pole \"$name\" nie jest równy wzorowi!"];
                    } else
                        return ["suc" => 0, "desc" => "(Wzór) Błędny wzór na string (dla: $name)!"];
                } else if ($pat[0] == "b") {
                    if (!($pat == "b(1)" || $pat == "b(0)"))
                        return ["suc" => 0, "desc" => "(Wzór) Błędny wzór na boolean! (dla: $name)"];

                    if ($pat == "b(1)" && !$value)
                        return ["suc" => 0, "desc" => "Pole \"$name\" nie jest równa wzorowi!"];
                    else if ($pat == "b(0)" && $value)
                        return ["suc" => 0, "desc" => "Pole \"$name\" nie jest równa wzorowi!"];
                } else if ($pat[0] == "i") {
                    if (preg_match('/i\((-?\d+)-(-?\d+)\)/', $pat, $matches))
                    {
                        $matches[1] = (int)($matches[1]);
                        $matches[2] = (int)($matches[2]);
                        if($matches[1] > $matches[2])
                            return ["suc" => 0, "desc" => "(Wzór) Pierwsza wartość jest większa od drugiej! (dla: $name)"];
                        if($value < $matches[1] || $value > $matches[2])
                            return ["suc" => 0, "desc" => "Liczba nie spełnia wymogu zakresu! (dla: $name)"];
                    }
                    elseif (preg_match('/i\((-?\d+)\)/', $pat, $matches))
                    {
                        if($matches[1] != $value)
                            return ["suc" => 0, "desc" => "Liczba nie jest równa wzorowi! (dla: $name)"];
                    }
                    else
                        return ["suc" => 0, "desc" => "(Wzór) Błędny wzór na integer! (dla: $name)"];
//                    if (preg_match('/i\((\d+)-(\d+)\)/', $pat, $matches)) {
//                        if ($matches[1] > $matches[2])
//                            return ["suc" => 0, "desc" => "(Wzór) Pierwsza wartość jest większa od drugiej!", "element_index" => $i];
//                        if ($value < $matches[1] || $value > $matches[2])
//                            return ["suc" => 0, "desc" => "Liczba nie spełnia wymogu zakresu!", "element_index" => $i];
//                    } else if (preg_match('/i\((.*?)\)/', $pat, $matches)) {
//                        if ($matches[1] != $value)
//                            return ["suc" => 0, "desc" => "Liczba nie jest równa wzorowi!", "element_index" => $i];
//                    } else
//                        return ["suc" => 0, "desc" => "(Wzór) Błędny wzór na integer!", "element_index" => $i];
                }
//                TODO decimal
            }
            $i++;
        }
        return ["suc" => 1, "desc" => "Walidacja przebiegła pomyślnie!"];
    }
}

//var_dump(Validator::validate(["12345678901a",true,2],["s(12)","b(1)","i(2)"]));