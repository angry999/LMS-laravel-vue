<?php

namespace App\Unit;

use SimpleXMLElement;
use App\JsonObject;
use App\Unit;

class Quiz extends JsonObject
{
    /**
     * @var array 
     */ 
    protected $questions;
    
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
        $this->questions = collect([]);
        $this->collectQuestions($data);
    }
    
    /**
     * Convert the quiz instance to an array
     *
     * @return array
     */ 
    public function toArray()
    {
        return [
            'questions' => $this->questions,
        ];
    }
    
    /**
     * Search and collect questions in a node
     * 
     * @param SimpleXMLElement $node
     */ 
    protected function collectQuestions(SimpleXMLElement $node)
    {
        foreach ($node->Question as $question) {
            $this->questions->push(new Quiz\Question($this->unit, $question));
        }
    }
}