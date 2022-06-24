<?php

namespace App\LMS;

use Exception;

class LmsException extends Exception
{
    const HTTP_ERROR = 1001;
    const MALFORMED_RESPONSE = 1002;
    
    const MALFORMED_DATA = 1;
    const INVALID_CREDENTIALS = 2;
    const LICENSE_EXPIRED = 3;
    const INVALID_USERNAME = 5;
    const INVALID_UNIT = 10;
    const ALREADY_LOGGED_IN = 11;
    const SESSION_EXPIRED = 18;
    
    public function __construct($code, $message)
    {
        parent::__construct($message, $code);
    }
}