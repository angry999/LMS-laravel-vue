<?php

namespace App\Unit\Dialog;

use SimpleXMLElement;
use App\JsonObject;
use App\Unit;

abstract class Node extends JsonObject
{
    /**
     * @var Unit
     */ 
    protected $unit;
    
    /**
     * @var string
     */
    protected $text;
    
    /**
     * @var string
     */
    protected $start_snapshot;
    
    /**
     * @var string
     */
    protected $end_snapshot;
    
    /**
     * @var string
     */
    protected $video;
    
    /**
     * @var string
     */
    protected $audio;
    
    /**
     * @var integer
     */
    protected $sentence_id;
    
    /**
     * @param Unit $unit
     * @param SimpleXMLElement $data
     */ 
    public function __construct(Unit $unit, SimpleXMLElement $data)
    {
        $this->unit = $unit;
        $this->text = $this->unit->getString((string) $data['sentenceStringId']);
        if ($data['video']) {
            $this->start_snapshot = (string) $data['startSnapshot'];
            $this->end_snapshot = (string) $data['endSnapshot'];
            $this->video = (string) $data['video'];
        } else {
            $this->start_snapshot = (string) $data['image'];
            $this->audio = (string) $data['audio'];
        }
        $this->sentence_id = (int) $data['sentenceStringId'];
    }
    
    /**
     * Convert the node instance to an array
     *
     * @return array
     */ 
    public function toArray()
    {
        return [
            'text' => $this->text,
            'start_snapshot' => $this->start_snapshot,
            'end_snapshot' => $this->end_snapshot,
            'video' => $this->video,
            'audio' => $this->audio,
            'sentence_id' => $this->sentence_id,
        ];
    }
}