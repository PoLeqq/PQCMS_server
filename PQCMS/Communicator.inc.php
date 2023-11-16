<?php

/**
 * Reprezentuje komunikator client server - cms server
 */
class Communicator
{
    /**
     *
     * @param string $path ścieżka linku do API (Najlepiej skorzystać z CommunicateURL)
     * @param string $secureKey klucz licencyjny (podczas VERIFY_LICENSE przesłać pusty)
     * @param array $postData dane, które zostaną przesłane metodą POST. (podczas VERIFY_LICENSE przesłać pustą)
     * @return mixed|null zwraca return (json) z danego APIka (lub null, gdy połączenie nie powiedzie się)
     */
    public static function communicate(string $path, string $secureKey, array $postData)
    {
        if($path == CommunicateURL::VERIFY_LICENSE)
        {
            require_once("config/data/JSONPQCMS.php");
            $pqcms = new JSONPQCMS();
            $postData = array(
                'domain' => $pqcms->getDomain(),
                'login' => $pqcms->getLogin(),
                'license_key' => $pqcms->getLicenseKey(),
                'generate_secure_key' => true
            );
        }
        else
        {
            $postData["domain"] = $_SERVER["SERVER_NAME"];
            $postData["secure_key"] = $secureKey;
        }

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($postData)
            )
        );

        $targetUrl = 'http://localhost/pqcms/server/api/'.$path;

        $context = stream_context_create($options);
        $response = file_get_contents($targetUrl, false, $context);

//        var_dump($response);

        if($response === false) return null;
        else return json_decode($response,true);
    }
}

class CommunicateURL
{
    public const VERIFY_LICENSE = "website/license/VerifyLicense.php";
}

//var_dump(Communicator::communicate(CommunicateURL::VERIFY_LICENSE,"",[]));