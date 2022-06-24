<?php

namespace App\Unit\Vocabulary;

use SimpleXMLElement;
use App\JsonObject;
use App\Unit;

class Word extends JsonObject
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
    protected $text;
    
    /**
     * @var string
     */
    protected $image;
    
    /**
     * @var string
     */
    protected $description;
    
    /**
     * @var array
     */
    protected $references;
    
    /**
     * @var array
     */ 
    protected $tip;
    
    /**
     * @param Unit $unit
     * @param SimpleXMLElement $data
     */ 
    public function __construct(Unit $unit, SimpleXMLElement $data)
    {
        $this->unit = $unit;
        $this->id = (string) $data['id'];
        $this->text = (string) $data['text'];
        $this->image = (string) $data['picture'];
        $this->description = trim((string) $data->Description);
        $this->references = collect([]);
        foreach ($data->ReferredSentence as $reference) {
            $this->references->push([
                'sentence_id' => (int) $reference['id'],
                'text' => $unit->getString((string) $reference['id']),
                'indices' => array_map('intval', explode(',', (string) $reference['atIndices'])),
            ]);
        }
        $this->tip = array_filter([
            'word' => trim((string) $data->TipWord),
            'unit' => (string) $data->Tip['unit'],
            'text' => trim((string) $data->Tip),
        ]);
    }
    
    /**
     * Convert the word instance to an array
     *
     * @return array
     */ 
    public function toArray()
    {
        return array_filter([
            'id' => $this->id,
            'text' => $this->text,
            'image' => $this->image,
            'description' => $this->description,
            'references' => $this->references,
            'tip' => $this->tip,
        ]);
    }
}