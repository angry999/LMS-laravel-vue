<?php

namespace App\Unit;

use SimpleXMLElement;
use App\Unit;
use App\JsonObject;

class Dialog extends JsonObject
{
    /**
     * @var array 
     */ 
    protected $tree;
    
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
        $this->tree = new Dialog\DeviceNode($this->unit, $data->DeviceRootNode);
    }

    /**
     * Convert the dialog instance to an array
     *
     * @return array
     */ 
    public function toArray()
    {
        return [
            'tree' => $this->tree,
        ];
    }
}