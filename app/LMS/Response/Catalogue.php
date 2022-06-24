<?php 

namespace App\LMS\Response;

use App\LMS\LmsException;
use App\Catalogue as CatalogueObject;

class Catalogue extends BaseResponse
{
    protected $export = [
        'Categories',
    ];
    
    public function getCategories()
    {
        return new CatalogueObject($this->result->UnitsMetadata->Categories);
    }
}