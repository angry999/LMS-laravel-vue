<?php

namespace App\LMS\Request;

class Stat extends AbstractRequest
{
    const P_SESSION_ID = 'session_id';
    
    public function __construct($client, $session_id)
    {
        parent::__construct($client);
        
        $this->setQuery([
            self::P_SESSION_ID => $session_id,
        ]);
    }
    
    public function getServiceName()
    {
        return 'ProgressStat';
    }
}