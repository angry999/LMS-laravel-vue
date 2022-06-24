<?php

namespace App\Unit\Dialog;

use SimpleXMLElement;
use App\Unit;

class UserNode extends Node
{
    /**
     * @var int
     */ 
    protected $node_id;
    
    /**
     * @var DeviceNode
     */
    protected $next;
    
    /**
     * @param Unit $unit
     * @param SimpleXMLElement $data
     */ 
    public function __construct(Unit $unit, SimpleXMLElement $data)
    {
        parent::__construct($unit, $data);
        $this->node_id = (int) $data['optionNodeId'];
        if ($data->count() > 0) {
            $this->next = new DeviceNode($this->unit, $data->NextDeviceSideNode);
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
            'node_id' => $this->node_id,
            'next' => $this->next,
        ];
    }
}