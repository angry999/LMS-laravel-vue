<?php

namespace App\LMS\Request;

class Progress extends AbstractRequest
{
    const P_SESSION_ID = 'session_id';
    const P_SERVER_TIME = 'server_utc_time';
    const P_CATALOG_V3 = 'catalog_v3';
    
    public function __construct($client, $session_id, $since)
    {
        parent::__construct($client);
        
        $this->setQuery([
            self::P_SESSION_ID => $session_id,
            self::P_SERVER_TIME => $this->formatTimestamp($since),
            self::P_CATALOG_V3 => 'true',
        ]);
    }
    
    public function getServiceName()
    {
        return 'ClientProgressReportV3';
    }
}