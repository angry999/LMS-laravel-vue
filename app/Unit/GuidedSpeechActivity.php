<?php

namespace App\Unit;

use SimpleXMLElement;
use App\JsonObject;
use App\Unit;

class GuidedSpeechActivity extends JsonObject
{
    /**
     * @var Unit
     */ 
    protected $unit;
    
    /**
     * @var array
     */ 
    protected $guidedspeeches;
    
    /**
     * @param Unit $unit
     * @param SimpleXMLElement $data
     */ 
    public function __construct(Unit $unit, SimpleXMLElement $data)
    {
        $this->unit = $unit;
        $this->guidedspeeches = collect([]);
        $this->collectGuidedSpeeches($data);
    }
    
    /**
     * Convert the GuidedSpeechActivity instance to an array
     *
     * @return array
     */ 
    public function toArray()
    {
        return [
            'guidedspeeches' => $this->guidedspeeches,
        ];
    }
    
    /**
     * Search and collect guidedspeeches in a node
     * 
     * @param SimpleXMLElement $node
     */ 
    protected function collectGuidedSpeeches(SimpleXMLElement $node)
    {
        foreach ($node->GuidedSpeech as $guidedspeech) {
            $this->guidedspeeches->push(new GuidedSpeechActivity\GuidedSpeech($this->unit, $guidedspeech));
        }
    }
}