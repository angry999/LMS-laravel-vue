<?php 

namespace App\LMS\Response;

use App\LMS\LmsException;

class License extends BaseResponse
{
    protected $export = [
        'Username',
        'Categories',
        'ExpirationUtcTime' => 'expiration_time',
        'Trial',
    ];
    
    public function getCategories()
    {
        return explode(',', $this->getKey('Categories'));
    }
    
    public function getExpirationUtcTime()
    {
        return Carbon::parse($this->getKey('ExpirationUtcTime'));
    }
    
    public function getTrial()
    {
        return $this->getKey('Trial') === 'true';
    }
}