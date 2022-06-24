<?php

namespace App;

use JsonSerializable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use SimpleXMLElement;

abstract class JsonObject implements Arrayable, Jsonable, JsonSerializable
{
    /**
     * Convert the unit instance to an array
     *
     * @return array
     */ 
    abstract public function toArray();

    /**
     * Convert the unit instance to JSON
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw Exception(json_last_error_msg());
        }

        return $json;
    }
    
    /**
     * Convert the object into something JSON serializable
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
    
    /**
     * Import values from XML element
     * 
     * @param SimpleXMLElement $xml
     * @param array $mapping
     * @param Closure $filter
     */ 
    public function importFromXmlElement(SimpleXMLElement $xml, $mapping = null, $filter = 'strval')
    {
        foreach ($xml as $node) {
            if ($mapping === null) {
                $this->{snake_case($node->getName())} = call_user_func($filter, $node);
            } else {
                $key = array_search($node->getName(), $mapping);
                if ($key !== false) {
                    if (is_numeric($key)) {
                        $this->{snake_case($node->getName())} = call_user_func($filter, $node);
                    } else {
                        $this->{$key} = call_user_func($filter, $node);
                    }
                }
            }
        }
    }
}