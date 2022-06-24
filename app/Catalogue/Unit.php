<?php

namespace App\Catalogue;

use SimpleXMLElement;
use App\JsonObject;

class Unit extends JsonObject
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
    protected $level;
    
    /**
     * @var string
     */ 
    protected $duration;
    
    /**
     * @var int
     */ 
    protected $sentences;
    
    /**
     * @var string
     */ 
    protected $thumbnail_image_url;
    
    /**
     * @var string
     */
    protected $strip_image_url;
    
    /**
     * @var string
     */ 
    protected $unit_type;
    
    /**
     * @var int
     */ 
    protected $unit_size;
    
    /**
     * @var bool
     */ 
    protected $is_free;
    
    /**
     * @var int
     */ 
    protected $sort_position; 
    
    /**
     * @param SimpleXMLElement $data
     */ 
    public function __construct(SimpleXMLElement $data)
    {
        $this->importFromXmlElement($data->attributes(), [
            'id' => 'unitId',
            'name',
            'level',
            'duration',
            'thumbnailImageUrl',
            'StripImageUrl',
            'unitType',
        ]);
        $this->importFromXmlElement($data->attributes(), [
            'sentences',
            'unitSize',
            'sort_position' => 'sortValue',
        ], 'intval');
        $this->is_free = 'true' === (string) $data['isFree'];
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
            'level' => $this->level,
            'duration' => $this->duration,
            'sentences' => $this->sentences,
            'thumbnail_image_url' => $this->thumbnail_image_url,
            'strip_image_url' => $this->strip_image_url,
            'unit_type' => $this->unit_type,
            'unit_size' => $this->unit_size,
            'is_free' => $this->is_free,
            'sort_position' => $this->sort_position,
        ];
    }
}