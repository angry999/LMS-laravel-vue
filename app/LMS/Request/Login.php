<?php

namespace App\LMS\Request;

class Login extends AbstractRequest
{
    const P_VENDOR_ID = 'vendor_id';
    const P_PASSWORD = 'password';
    const P_APP_ID = 'app_id';
    const P_IMEI = 'imei';
    const P_USER_NAME = 'user_name';
    const P_PLATFORM = 'platform';
    const P_CATALOG_V3 = 'catalog_v3';
    const P_REGID = 'regid';
    
    public function __construct($client, $username, $password)
    {
        parent::__construct($client);
        
        $this->setQuery([
            self::P_VENDOR_ID => $client->getVendorId(),
            self::P_PASSWORD => $password,
            self::P_APP_ID => $client->getAppId(),
            self::P_IMEI => '',
            self::P_USER_NAME => $username,
            self::P_PLATFORM => $client->getPlatform(),
            self::P_CATALOG_V3 => 'true',
            self::P_REGID => '',
        ]);
    }
    
    public function getServiceName()
    {
        return 'Authenticate';
    }
}