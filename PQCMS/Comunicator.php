<?php

class Comunicator
{
    public static function comunicate(ComunicateURL $path, string $secureKey, array $posts)
    {
        if($path == ComunicateURL::VERIFY_LICENSE)
        {
            $postData = array(
                'domain' => $_SERVER["SERVER_NAME"],
                'secure_key' => $secureKey,
            );
        }
        $targetUrl = 'http://localhost/pqcms/server/api/'.$path;


        $options = array('http' => $posts);

        $context = stream_context_create($options);
        $response = file_get_contents($targetUrl, false, $context);

        if($response === false) return null;
        else
        {
            $json = json_decode($response,true);
            if(isset($json["secure_key"])) return $json["secure_key"];
        }

        return null;
    }
}

class ComunicateURL
{
    public const VERIFY_LICENSE = "website/license/VerifyLicense.php";
}

echo gethostname();