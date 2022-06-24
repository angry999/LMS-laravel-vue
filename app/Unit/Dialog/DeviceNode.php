<?php

namespace App\Unit\Dialog;

use SimpleXMLElement;
use App\Unit;

class DeviceNode extends Node
{
    /**
     * @var array
     */
    protected $options;
    
    /**
     * @param Unit $unit
     * @param SimpleXMLElement $data
     */ 
    public function __construct(Unit $unit, SimpleXMLElement $data)
    {
        parent::__construct($unit, $data);
        
        $this->options = collect([]);
        foreach ($data->UserOption as $option) {
            $this->options->push(new UserNode($this->unit, $option));
        }
    }
    
    /**
     * Convert the quiz instance to an array
     *
     * @return array
     */ 
    public function toArray()
    {
        return parent::toArray() + [
            'node_id' => $this->sentence_id,
            'options' => $this->options,
        ];
    }
}