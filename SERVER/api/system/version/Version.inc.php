<?php

class Version
{
    static function getVersion($nodeName,$complex): string|array
    {
        $file = json_decode(file_get_contents(dirname(__DIR__, 3) . "/version.json"),true);

        if($complex) return $file[$nodeName];

        $ver = $file[$nodeName];
        return $ver["type"]."-".$ver["major"].".".$ver["minor"].".".$ver["patch"]." (".Version::stringifyDate($ver["date"]) .")";
    }

    static function stringifyDate($versionDateArray): string
    {
        $year = $versionDateArray["year"];
        $month = $versionDateArray["month"];
        $day = $versionDateArray["day"];

        $date = $year."-";

        if($month < 10) $month = "0".$month;
        if($day < 10) $day = "0".$day;

        $date .= $month."-";
        $date .= $day;

        return $date;
    }
}