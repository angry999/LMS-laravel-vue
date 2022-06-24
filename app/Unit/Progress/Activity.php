<?php

namespace App\Unit\Progress;

use Masterfri\SmartRelations\SmartRelations;
use Illuminate\Database\Eloquent\Model;
use App\Unit\Progress;

class Activity extends Model
{
    use SmartRelations;
    
    /**
     * @var string
     */ 
    protected $table = 'unit_activity_progress';
    
    /**
     * @var array
     */ 
    protected $casts = [
        'activity_number' => 'integer',
        'last_score' => 'integer',
        'best_score' => 'integer',
    ];
    
    /**
     * Get link to the unit progress
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function progress()
    {
        return $this->belongsTo(Progress::class, 'unit_progress_id');
    }
}