<?php

namespace App\Unit\Progress;

use Masterfri\SmartRelations\SmartRelations;
use Illuminate\Database\Eloquent\Model;
use App\Unit\Progress;

abstract class AbstractReport extends Model
{
    use SmartRelations;
    
    /**
     * Get link to the unit progress
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function progress()
    {
        return $this->belongsTo(Progress::class, 'unit_progress_id');
    }
    
    /**
     * Get Unit ID
     * 
     * @return string
     */ 
    public function getUnitIdAttribute()
    {
        return $this->progress->unit;
    }
    
    /**
     * Mark report as non-actual
     */ 
    public function setNonActual()
    {
        static::where('id', '=', $this->id)->update([
            'is_actual' => 0,
        ]);
    }
}