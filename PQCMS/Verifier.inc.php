<?php

class Verifier
{
    public static function electroCMS_verify(bool $generateKey): ?string
    {
        require_once(__DIR__."/config/data/JSONPQCMS.php");
        $pqcmsData = new JSONPQCMS();

        require_once("Comunicator.php");
        Comunicator::comunicate(ComunicateURL::VERIFY_LICENSE,);

//        require_once(__DIR__."/config/data/JSONPQCMS.php");
//        $pqcmsData = new JSONPQCMS();
//
//        $targetUrl = 'http://localhost/pqcms/server/api/website/license/VerifyLicense.php';
//        $postData = array(
//            'domain' => $pqcmsData->getDomain(),
//            'login' => $pqcmsData->getLogin(),
//            'license_key' => $pqcmsData->getLicenseKey(),
//            'generate_secure_key' => $generateKey
//        );
//
//        $options = array(
//            'http' => array(
//                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
//                'method'  => 'POST',
//                'content' => http_build_query($postData)
//            )
//        );
//
//        $context = stream_context_create($options);
//        $response = file_get_contents($targetUrl, false, $context);
//
//        if($response === false) return null;
//        else
//        {
//            $json = json_decode($response,true);
//            if(isset($json["secure_key"])) return $json["secure_key"];
//        }
//
//        return null;
    }
}