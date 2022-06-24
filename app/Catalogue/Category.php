<?php

namespace App\Catalogue;

use SimpleXMLElement;
use App\JsonObject;

class Category extends JsonObject
{
    /**
     * @var string
     */ 
    protected $id;
    
    /**
     * @var string
     */ 
    protected $name;
    
    /**
     * @var string
     */ 
    protected $display_name;
    
    /**
     * @var string
     */ 
    protected $color;
    
    /**
     * @var string
     */ 
    protected $description;
    
    /**
     * @var int
     */ 
    protected $start_level;
    
    /**
     * @var int
     */ 
    protected $end_level;
    
    /**
     * @var int
     */ 
    protected $free_units;
    
    /**
     * @var int
     */ 
    protected $unlocked_units;
    
    /**
     * @var string
     */ 
    protected $image_url;
    
    /**
     * @var array
     */ 
    protected $units;
    
    /**
     * @param SimpleXMLElement $data
     */ 
    public function __construct(SimpleXMLElement $data)
    {
        $this->importFromXmlElement($data->attributes(), [
            'id',
            'displayName',
            'name' => 'tabName',
            'color',
            'description',
            'imageUrl',
        ]);
        $this->importFromXmlElement($data->attributes(), [
            'startLevel',
            'endLevel',
            'freeUnits',
            'unlockedUnits',
        ], 'intval');
        $this->units = collect([]);
        $this->collectUnits($data);
    }
    
    /**
     * Convert the category instance to an array
     *
     * @return array
     */ 
    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'color' => $this->color,
            'description' => $this->description,
            'start_level' => $this->start_level,
            'end_level' => $this->end_level,
            'free_units' => $this->free_units,
            'unlocked_units' => $this->unlocked_units,
            'image_url' => $this->image_url,
            'units' => $this->units,
        ];
    }
    
    /**
     * Collect units
     * 
     * @param SimpleXMLElement $node
     */ 
    protected function collectUnits(SimpleXMLElement $node)
    {
        foreach ($node->UnitMetadata as $unit) {
            $this->units->push(new Unit($unit));
        }
    }
}