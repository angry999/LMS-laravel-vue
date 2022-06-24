<?php

namespace App\Unit;

use SimpleXMLElement;
use App\JsonObject;
use App\Unit;

class Listening extends JsonObject
{
    /**
     * @var string
     */ 
    protected $video;
    
    /**
     * @var array 
     */ 
    protected $subtitles;
    
    /**
     * @var Unit
     */ 
    protected $unit;
    
    /**
     * @param Unit $unit
     * @param SimpleXMLElement $data
     */ 
    public function __construct(Unit $unit, SimpleXMLElement $data)
    {
        $this->unit = $unit;
        $this->video = (string) $data['video'];
        $this->subtitles = collect([]);
        foreach ($data->Subtitles->Subtitle as $subtitle) {
            $this->subtitles->push([
                'text' => $unit->getString((string) $subtitle['subtitleStringId']),
                'speaker' => (int) $subtitle['speakerIndex'],
                'time' => (int) $subtitle['subtitleMilisecondsOffset'],
                'sentence_id' => (int) $subtitle['subtitleStringId'],
            ]);
        }
    }
    
    /**
     * Convert the listening instance to an array
     *
     * @return array
     */ 
    public function toArray()
    {
        return [
            'video' => $this->video,
            'subtitles' => $this->subtitles,
        ];
    }
}