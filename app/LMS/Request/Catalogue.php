<?php

namespace App\LMS\Request;

class Catalogue extends AbstractRequest
{
    const P_SESSION_ID = 'session_id';
    const P_CATALOG_V3 = 'catalog_v3';
    
    public function __construct($client, $session_id)
    {
        parent::__construct($client);
        
        $this->setQuery([
            self::P_SESSION_ID => $session_id,
            self::P_CATALOG_V3 => 'true',
        ]);
    }
    
    public function getServiceName()
    {
        return 'ListUnitsMetadata';
    }
}