<?php 

namespace App\LMS\Response;

use App\LMS\LmsException;
use Exception;

class BaseResponse
{
    protected static $errorMessages = [
        LmsException::INVALID_CREDENTIALS => 'Invalid username or password',
        LmsException::LICENSE_EXPIRED => 'License expired',
        LmsException::ALREADY_LOGGED_IN => 'Already logged in',
        LmsException::INVALID_USERNAME => 'Invalid username',
        LmsException::MALFORMED_DATA => 'Malformed data',
        LmsException::INVALID_UNIT => 'Invalid unit',
        LmsException::SESSION_EXPIRED => 'Session expired',
    ];
    
    protected $result;
    protected $export = [];
    
    public function fill($xml)
    {
        $this->result = simplexml_load_string($xml);
        
        if (!$this->result) {
            throw new LmsException(LmsException::MALFORMED_RESPONSE, 'Invalid response given: ' . $xml);
        }
        
        $this->checkErrors();
    }
    
    protected function checkErrors()
    {
        $code = $this->getResultCode();
        if ($code !== 0) {
            if (isset(self::$errorMessages[$code])) {
                throw new LmsException($code, self::$errorMessages[$code]);
            } else {
                throw new LmsException($code, 'Unknown error');
            }
        }
    }
    
    public function getResultCode()
    {
        return (int) $this->result->ResultCode;
    }
    
    public function getResult()
    {
        return $this->result;
    }
    
    public function toArray()
    {
        $result = [];
        foreach ($this->export as $key => $value) {
            if (is_numeric($key)) {
                $result[snake_case($value)] = call_user_func([$this, 'get' . $value]);
            } else {
                $result[$value] = call_user_func([$this, 'get' . $key]);
            }
        }
        return $result;
    }
    
    public function getKey($key)
    {
        $value = $this->result->{$key};
        if ($value->count() == 0) {
            throw new Exception('Access to undefined key ' . $key . ' on ' . get_class($this));
        } elseif ($value->count() == 1) {
            return (string) $value;
        } else {
            throw new Exception('Access to non-scalar key ' . $key . ' on ' . get_class($this));
        }
    }
    
    public function __call($func, $arguments)
    {
        if (starts_with($func, 'get') && count($arguments) == 0) {
            return $this->getKey(substr($func, 3));
        } else {
            throw new Exception('Call to undefined method ' . $func . ' on ' . get_class($this));
        }
    }
}