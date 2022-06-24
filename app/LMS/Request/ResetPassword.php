<?php

namespace App\LMS\Request;

class ResetPassword extends AbstractRequest
{
    const P_EMAIL = 'email';
    
    public function __construct($client, $email)
    {
        parent::__construct($client);
        
        $this->setQuery([
            self::P_EMAIL => $email,
        ]);
    }
    
    public function getServiceName()
    {
        return 'ForgotCredentials';
    }
}