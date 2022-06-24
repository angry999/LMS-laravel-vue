<?php

namespace App\LMS;

use GuzzleHttp\Client as GuzzleClient;
use Log;
use App\Unit\Progress\ReadingReport;
use App\Unit\Progress\QuizReport;

class Client
{
    const BASE_URL = 'https://lms.speakingpal.com/services/speakingpal_rest/';
    
    protected $vendor_id;
    protected $app_id;
    protected $platform;
    
    public function __construct($vendor_id, $app_id, $platform)
    {
        $this->vendor_id = $vendor_id;
        $this->app_id = $app_id;
        $this->platform = $platform;
    }
    
    public function login($username, $password)
    {
        return $this->get(new Request\Login($this, $username, $password), new Response\Login());
    }
    
    public function resetPassword($username)
    {
        return $this->get(new Request\ResetPassword($this, $username), new Response\ResetPassword());
    }
    
    public function catalogue($session_id)
    {
        return $this->get(new Request\Catalogue($this, $session_id), new Response\Catalogue());
    }
    
    public function reportReadingProgress($session_id, ReadingReport $report)
    {
        return $this->post(new Request\ReadingReport($this, $session_id, $report), new Response\ReadingReport());
    }
    
    public function reportQuizProgress($session_id, QuizReport $report)
    {
        return $this->post(new Request\QuizReport($this, $session_id, $report), new Response\QuizReport());
    }
    
    public function getStat($session_id)
    {
        return $this->get(new Request\Stat($this, $session_id), new Response\Stat());
    }
    
    public function getProgress($session_id, $since = 0)
    {
        return $this->get(new Request\Progress($this, $session_id, $since), new Response\Progress());
    }
    
    protected function get($request, $response)
    {
        $client = new GuzzleClient();
        $url = $this->buildUrl($request->getServiceName(), $request->getQuery());
        Log::debug(sprintf('LMS API Request: %s', $url));
        $result = $client->get($url);
        return $this->processResult($result, $response);
    }
    
    protected function post($request, $response)
    {
        $client = new GuzzleClient();
        $url = $this->buildUrl($request->getServiceName(), $request->getQuery());
        $body = $request->getBody();
        Log::debug(sprintf('LMS API Request: %s', $url));
        Log::debug(sprintf('POST data: %s', json_encode($body)));
        $result = $client->post($url, [
            'body' => $body,
        ]);
        return $this->processResult($result, $response);
    }
    
    protected function processResult($result, $response)
    {
        $code = $result->getStatusCode();
        if ($code < 200 || $code >= 300) {
            Log::debug(sprintf('LMS API Http Error: %s', $code));
            throw new LmsException(LmsException::HTTP_ERROR, 'Unexpected HTTP status code given:' . $code);
        }
        Log::debug(sprintf('LMS API Response: %s', $result->getBody()));
        $response->fill($result->getBody());
        return $response;
    }
    
    protected function buildUrl($serviceName, $query = null)
    {
        return self::BASE_URL . $serviceName . ($query ? ('?' . http_build_query($query)) : '');
    }
    
    public function getVendorId()
    {
        return $this->vendor_id;
    }
    
    public function getAppId()
    {
        return $this->app_id;
    }
    
    public function getPlatform()
    {
        return $this->platform;
    }
}
