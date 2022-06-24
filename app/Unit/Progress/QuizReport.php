<?php

namespace App\Unit\Progress;

use App\Unit\Progress;

class QuizReport extends AbstractReport
{
    /**
     * @var string
     */ 
    protected $table = 'quiz_report';
    
    /**
     * @var array
     */ 
    protected $casts = [
        'results' => 'array',
        'correct' => 'integer',
        'total' => 'integer',
    ];
    
    /**
     * @inheritdoc
     */ 
    protected static function boot() 
    {
        parent::boot();
        
        static::saving(function($report) {
            if ($report->is_sync != 1) {
                $report->calcScore();
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
     * Calculate score
     */ 
    public function calcScore()
    {
        $this->total = count($this->results);
        $this->correct = count(array_filter($this->results));
    }
    
    /**
     * Get percent value of correct answers
     * 
     * @return int
     */ 
    public function getPercent()
    {
        return $this->total > 0 ? (int) round(100 * $this->correct / $this->total) : 0;
    }
    
    /**
     * Get previous actual report
     * 
     * @return QuizReport|null
     */ 
    public function getPreviousActualReport()
    {
        return static::where('unit_progress_id', '=', $this->unit_progress_id)
            ->where('is_actual', '=', 1)
            ->first();
    }
    
    /**
     * Check is the report is improved
     * 
     * @param QuizReport $compare_to
     * 
     * @return bool
     */ 
    public function isImproved(QuizReport $compare_to)
    {
        return $this->correct > $compare_to->correct;
    }
    
    /**
     * Create report from sync
     * 
     * @param Progress $progress
     * @param array $data
     * 
     * @return QuizReport
     */ 
    public static function sync(Progress $progress, $data)
    {
        $report = new static();
        $report->is_sync = 1;
        $report->progress = $progress;
        $report->total = $data['total'];
        $report->correct = $data['correct'];
        $report->results = [];
        $report->save();
        return $report;
    }
}