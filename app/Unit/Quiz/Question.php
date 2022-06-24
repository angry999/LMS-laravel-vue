<?php

namespace App\Unit\Quiz;

use SimpleXMLElement;
use App\JsonObject;
use App\Unit;

class Question extends JsonObject
{
    /**
     * @var Unit
     */ 
    protected $unit;
    
    /**
     * @var string
     */
    protected $id;
    
    /**
     * @var string
     */
    protected $question;
    
    /**
     * @var array 
     */ 
    protected $answers;
    
    /**
     * @param Unit $unit
     * @param SimpleXMLElement $data
     */ 
    public function __construct(Unit $unit, SimpleXMLElement $data)
    {
        $this->unit = $unit;
        $this->id = (string) $data['questionId'];
        $this->question = $unit->getString((string) $data['questionStringId']);
        $this->answers = collect([]);
        foreach ($data->Answer as $answer) {
            $this->answers->push($unit->getString((string) $answer['answerTextId']));
        }
    }
    
    /**
     * Convert the question instance to an array
     *
     * @return array
     */ 
    public function toArray()
    {
        return [
            'id' => $this->id,
            'question' => $this->question,
            'answers' => $this->answers,
        ];
    }
}