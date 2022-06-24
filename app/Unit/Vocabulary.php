<?php

namespace App\Unit;

use SimpleXMLElement;
use App\JsonObject;
use App\Unit;

class Vocabulary extends JsonObject
{
    /**
     * @var Unit
     */ 
    protected $unit;
    
    /**
     * @var array
     */ 
    protected $words;
    
    /**
     * @param Unit $unit
     * @param SimpleXMLElement $data
     */ 
    public function __construct(Unit $unit, SimpleXMLElement $data)
    {
        $this->unit = $unit;
        $this->words = collect([]);
        $this->collectWords($data);
    }
    
    /**
     * Convert the vocabulary instance to an array
     *
     * @return array
     */ 
    public function toArray()
    {
        return [
            'words' => $this->words,
        ];
    }
    
    /**
     * Search and collect words in a node
     * 
     * @param SimpleXMLElement $node
     */ 
    protected function collectWords(SimpleXMLElement $node)
    {
        foreach ($node->Word as $word) {
            $this->words->push(new Vocabulary\Word($this->unit, $word));
        }
    }
}