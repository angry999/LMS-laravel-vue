<?php

namespace App\Cdn;

use GuzzleHttp\Client as GuzzleClient;
use Exception;
use Cache;
use App\Unit;

class Client
{
    const BASE_URL = 'http://cdn.speakingpal.com/';
    const CACHE_TTL = 120;
    
    protected $folders = [
        Unit::FOLDER_UNITS => 'content/sites/default/Versions/web/v2',
        Unit::FOLDER_TRANSLATIONS => 'content/sites/default/translations',
    ];
    
    public function getFolderUrl($folder)
    {
        if (!isset($this->folders[$folder])) {
            throw new Exception('Unknown folder: ' . $folder);
        }
        return self::BASE_URL . $this->folders[$folder];
    }
    
    public function getFileUrl($file)
    {
        list($folder, $path) = explode('/', $file, 2);
        return $this->getFolderUrl($folder) . '/' . $path;
    }
    
    public function getFile($file)
    {
        return Cache::remember($file, self::CACHE_TTL, function() use($file) {
            return (string) $this->fetchFile($file);
        });
    }
    
    public function fetchFile($file)
    {
        $url = $this->getFileUrl($file);
        $client = new GuzzleClient();
        $result = $client->get($url);
        $code = $result->getStatusCode();
        if ($code < 200 || $code >= 300) {
            throw new Exception('File not found: ' . $file);
        }
        return $result->getBody();
    }
} 