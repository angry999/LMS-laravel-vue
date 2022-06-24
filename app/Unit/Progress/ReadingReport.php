<?php

namespace App\Unit\Progress;

use App\Unit\Progress;

class ReadingReport extends AbstractReport
{
    /**
     * @var string
     */ 
    protected $table = 'reading_reports';
    
    /**
     * @var array
     */ 
    protected $casts = [
        'activity_number' => 'integer',
        'average_score' => 'integer',
    ];
    
    /**
     * @inheritdoc
     */ 
    protected static function boot() 
    {
        parent::boot();
        
        static::saving(function($report) {
            if ($report->is_sync != 1) {
                $report->average_score = $report->calcAverageScore();
                $report->practiced_sentences = $report->sentences->count();
            }
            $prev = $report->getPreviousActualReport();
            if ($prev !== null && $report->isImproved($prev)) {
                $report->is_actual = 1;
                $prev->setNonActual();
            } elseif ($prev === null) {
                $report->is_actual = 1;
            }
        });
    }
    
    /**
     * Get related sentences
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */ 
    public function sentences()
    {
        return $this->hasMany(SentenceReport::class, 'reading_report_id');
    }
    
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
     * Calculate average score
     * 
     * @return int
     */ 
    public function calcAverageScore()
    {
        return max(1, round($this->sentences->avg(function($sentence) {
            return $sentence->grade;
        })));
    }
    
    /**
     * Get ASR session_id
     * 
     * @return string
     */ 
    public function getAsrSessionIdAttribute()
    {
        return md5(implode(':', [
            $this->id,
            $this->progress->user_id,
            $this->created_at,
        ]));
    }
    
    /**
     * Get previous actual report
     * 
     * @return ReadingReport|null
     */ 
    public function getPreviousActualReport()
    {
        return static::where('unit_progress_id', '=', $this->unit_progress_id)
            ->where('activity_number', '=', $this->activity_number)
            ->where('is_actual', '=', 1)
            ->first();
    }
    
    /**
     * Check is the report is improved
     * 
     * @param ReadingReport $compare_to
     * 
     * @return bool
     */ 
    public function isImproved(ReadingReport $compare_to)
    {
        return $this->average_score > $compare_to->average_score;
    }
    
    /**
     * Create report from sync
     * 
     * @param Progress $progress
     * @param array $data
     * 
     * @return ReadingReport
     */ 
    public static function sync(Progress $progress, $data)
    {
        $report = new static();
        $report->is_sync = 1;
        $report->progress = $progress;
        $report->activity_number = $data['id'];
        $report->average_score = $data['grade'];
        $report->practiced_sentences = $data['practiced_sentences'];
        $report->save();
        return $report;
    }
}