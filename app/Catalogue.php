<?php

namespace App;

use SimpleXMLElement;

class Catalogue extends JsonObject
{
    /**
     * @var \Illuminate\Support\Collection
     */ 
    protected $categories;
    
    /**
     * @param SimpleXMLElement $data
     */ 
    public function __construct(SimpleXMLElement $data)
    {
        $this->categories = collect([]);
        $this->collectCategories($data);
    }
    
    /**
     * Convert the catalogue instance to an array
     *
     * @return array
     */ 
    public function toArray()
    {
        return [
            'categories' => $this->categories,
        ];
    }
    
    /**
     * Collect categories
     * 
     * @param SimpleXMLElement $node
     */ 
    protected function collectCategories(SimpleXMLElement $node)
    {
        foreach ($node->Category as $category) {
            $this->categories->push(new Catalogue\Category($category));
        }
    }
}