<?php 

namespace App\LMS\Response;

use App\LMS\LmsException;

class Stat extends BaseResponse
{
    protected $export = [
        'speak',
    ];
    
    public function getSpeak()
    {
        return [
            'green' => (int) $this->result->stat->speak->green,
            'yellow' => (int) $this->result->stat->speak->yellow,
            'red' => (int) $this->result->stat->speak->red,
        ];
    }
}