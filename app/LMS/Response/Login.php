<?php 

namespace App\LMS\Response;

use App\LMS\ServerMessage;
use Carbon\Carbon;

class Login extends BaseResponse
{
    protected $export = [
        'SessionID' => 'session_id',
        'LmsSessionExpirationDate' => 'session_expiration_date',
        'Username',
        'SupportedLanguages',
    ];
    
    public function getSupportedLanguages()
    {
        return explode(',', $this->getKey('SupportedLanguages'));
    }
    
    public function getLmsSessionExpirationDate()
    {
        return Carbon::parse($this->getKey('LmsSessionExpirationDate'));
    }
    
    public function getServerMessages()
    {
        $messages = collect([]);
        foreach ($this->result->ServerMessages as $item) {
            $messages->push(new ServerMessage(
                (int) $item->id,
                (string) $item->title,
                (string) $item,
                'true' == (string) $item->showOnce
            ));
        }
        return $messages;
    }
}