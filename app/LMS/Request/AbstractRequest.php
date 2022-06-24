<?php

namespace App\LMS\Request;

use Carbon\Carbon;

abstract class AbstractRequest
{
    protected $query;
    protected $body;
    protected $client;
    
    abstract public function getServiceName();
    
    public function __construct($client)
    {
        $this->client = $client;
    }
    
    public function getClient()
    {
        return $this->client;
    }
    
    public function getQuery()
    {
        return $this->query;
    }
    
    public function setQuery($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->setQuery($k, $v);
            }
        } else {
            $this->query[$key] = $value;
        }
    }
    
    public function getBody()
    {
        return $this->body;
    }
    
    public function setBody($data)
    {
        $this->body = $data;
    }
    
    protected function formatTimestamp($time)
    {
        if ($time instanceof Carbon) {
            return sprintf('%d000', $time->getTimestamp());
        }
        return 0;
    }
}