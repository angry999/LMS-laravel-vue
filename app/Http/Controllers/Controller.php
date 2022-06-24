<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    const REASON_INVALID_CREDENTIALS = 'invalid_credentials';
    const REASON_LICENSE_EXPIRED = 'license_expired';
    const REASON_ALREADY_LOGGED_IN = 'already_logged_in';
    const REASON_UNKNOWN_ERROR = 'unknown_error';
    const REASON_SERVER_ERROR = 'server_error';
    const REASON_INVALID_USERNAME = 'invalid_username';
}
