<?php

class SQLSecurity
{
    /**
     * Funkcja sprawdza, jakie znaki posiada napis. Zwraca tablicę asocjacyjną, a wartościami są odpowiednio true/false
     * @param string $string napis
     * @param array $chars znaki do szukania (ew. napisy)
     * @return array tablica asocjacyjna (znak => (true/false)
     */
    public static function doesStringContains(string $string, array $chars = ['"','\'','-','#','%','_'], bool $reverse = false): array
    {
        $contains = [];
        if($reverse)
        {
            foreach($chars as $char)
                if(!str_contains($string, $char))
                    $contains[] = $char;
        }
        else
            foreach($chars as $char)
                if(str_contains($string, $char))
                    $contains[] = $char;
        return $contains;
    }

//    public static function checkValidCharacters($string,)

    public static function generateResponseForAPI(array $doesStringContainsResponse, string $fieldName): array
    {
        if(sizeof($doesStringContainsResponse) === 0)
            return [];
        $chars = implode(", ",$doesStringContainsResponse);
        return ["suc" => 0, "desc" => "Pole \"${fieldName}\" posiada niedozwolone znaki (${chars})!"];
    }

    public static function doesStringContainsSQLCharacters(string $string, array $chars): array
    {
        $contains = [];
        foreach($chars as $char)
        {
            if(str_contains($string, $char))
                $contains[$char] = true;
            else
                $contains[$char] = false;
        }
        return $contains;
    }

    public static function getInsecureCharacters(): array
    {
        return ['"','\'','-','#','%','_'];
    }

    public static function getKeyCharacters(): array
    {
        return ['0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f'];
    }

    public static function isSafeStringSecure(mysqli $conn, string $string): bool
    {
        foreach(self::getInsecureCharacters() as $char)
        {
            if(str_contains($string, $char))
                return false;
        }
        return $conn->real_escape_string($string) === $string;
    }
}