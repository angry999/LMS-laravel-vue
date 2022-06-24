<?php

namespace App\Unit\Progress;

use Masterfri\SmartRelations\SmartRelations;
use Illuminate\Database\Eloquent\Model;
use App\Unit\Progress;

class SentenceReport extends Model
{
    use SmartRelations;
    
    /**
     * @var string
     */ 
    protected $table = 'sentence_reports';
    
    /**
     * @var array
     */ 
    protected $fillable = [
        'sentence_id', 
        'node_id', 
        'grade'
    ];
    
    /**
     * @var array
     */ 
    protected $casts = [
        'sentence_id' => 'integer',
        'node_id' => 'integer',
        'grade' => 'integer',
    ];
    
    /**
     * Get link to the reading
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reading()
    {
        return $this->belongsTo(ReadingReport::class, 'reading_report_id');
    }
}