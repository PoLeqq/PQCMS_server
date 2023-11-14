<?php

class LicenseKey
{
    protected string $licenseKey;

    public function __construct(string $licenseKey)
    {
        if(strlen($licenseKey) != 23) throw new Error("License Key must be 23 char long!");
        $this->licenseKey = $licenseKey;
    }

    public function getLicenseKey(): string
    {
        return $this->licenseKey;
    }

    public static function getRandomLicenseKey(): string
    {
        $key = "";
        for($i=1; $i<20; $i++) {
            $key .= LicenseKey::randomChar();
            if($i%5==0) $key.='-';
        }
        $key .= LicenseKey::randomChar();
        return $key;
    }

    private static function randomChar(): int|string
    {
        return rand(0, 1) ? rand(0, 9) : chr(rand(65, 90));
    }
}