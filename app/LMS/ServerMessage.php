<?php

namespace App\LMS;

class ServerMessage
{
    public $id;
    public $title;
    public $text;
    public $once;
    
    public function __construct($id, $title, $text, $once = true)
    {
        $this->id = $id;
        $this->title = $title;
        $this->text = $text;
        $this->once = $once;
    }
}