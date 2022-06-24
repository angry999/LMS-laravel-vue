<?php

namespace App\Unit;

use Masterfri\SmartRelations\SmartRelations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\User;
use App\Unit;
use Carbon\Carbon;
use DB;
use Exception;

class Progress extends Model
{
    use SmartRelations;
    
    /**
     * @var string
     */ 
    protected $table = 'unit_progress';
    
    /**
     * @var array
     */ 
    protected $hidden = [
        'user',
    ];
    
    /**
     * @var array
     */ 
    protected $casts = [
        'activities_total' => 'integer',
        'activities_completed' => 'integer',
        'best_score' => 'integer',
        'quiz_completed' => 'boolean',
        'quiz_score' => 'integer',
    ];
    
    /**
     * Get link to the user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Completed reading activities
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activities()
    {
        return $this->hasMany(Progress\Activity::class, 'unit_progress_id');
    }
    
    /**
     * Filter by specified user/unit combination
     * 
     * @param Builder $builder
     * @param int $user_id
     * @param string $unit_id
     * 
     * @return Builder
     */ 
    public function scopeOfUnit(Builder $builder, $user_id, $unit_id)
    {
        $builder->where('user_id', '=', $user_id);
        $builder->where('unit', '=', $unit_id);
        return $builder;
    }
    
    /**
     * Save the record if not saved
     */ 
    public function persist()
    {
        if (!$this->exists) {
            $this->save();
        }
    }
    
    /**
     * Initialize user progress instance
     * 
     * @param User $user
     * @param Unit $unit
     * 
     * @return Progress
     */ 
    public static function init(User $user, Unit $unit)
    {
        $progress = new static();
        $progress->user = $user;
        $progress->unit = $unit->id;
        $progress->activities_total = $unit->countActivities();
        $progress->activities_completed = 0;
        $progress->best_score = 0;
        $progress->has_quiz = $unit->hasQuiz() ? 1 : 0;
        $progress->quiz_score = 0;
        return $progress;
    }
    
    /**
     * Update user progress based on the given report
     * 
     * @param Progress\ReadingReport $report
     */ 
    public function updateFromReadingReport(Progress\ReadingReport $report)
    {
        $activity = $this->getActivityByNumber($report->activity_number);
        $activity->last_score = $report->average_score;
        $activity->best_score = max($activity->best_score, $activity->last_score);
        $activity->save();
        $this->activities_completed = max($this->activities_completed, $report->activity_number);
        if ($this->completed_at === null && 
            $this->has_quiz == 0 && 
            $this->activities_completed >= $this->activities_total) 
        {
            $this->completed_at = Carbon::now();
        }
        if ($this->completed_at !== null) {
            $this->updateBestScore();
        }
        $this->save();
    }
    
    /**
     * Update user progress based on the given report
     * 
     * @param Progress\QuizReport $report
     */ 
    public function updateFromQuizReport(Progress\QuizReport $report)
    {
        $this->quiz_completed = true;
        $this->quiz_score = max($this->quiz_score, $report->getPercent());
        if ($this->completed_at === null) {
            $this->completed_at = Carbon::now();
            $this->updateBestScore();
        }
        $this->save();
    }
    
    /**
     * Get one of activities progress
     * 
     * @param int $number
     * 
     * @return Progress\Activity
     */ 
    public function getActivityByNumber($number)
    {
        $activity = $this->activities()->where('activity_number', '=', $number)->first();
        if ($activity === null) {
            $activity = new Progress\Activity();
            $activity->activity_number = $number;
            $activity->progress = $this;
        }
        return $activity;
    }
    
    /**
     * Update best score
     */ 
    protected function updateBestScore()
    {
        $avg = $this->activities()->avg('best_score');
        $this->best_score = max($this->best_score, round($avg));
    }
    
    /**
     * Check if the particular activity is locked
     * 
     * @param int $number
     * 
     * @return bool 
     */ 
    public function activityLocked($number)
    {
        return $number > $this->activities_completed + 1;
    }
    
    /**
     * Check if the quiz is locked
     * 
     * @return bool 
     */ 
    public function quizLocked()
    {
        return $this->activities_completed < $this->activities_total;
    }
    
    /**
     * Get overall user's stat
     * 
     * @param int $userId
     * 
     * @return array
     */ 
    public static function getStat($userId)
    {
        return [
            'speaking' => static::getSpeakingStat($userId),
            'quiz' => static::getQuizStat($userId),
            'units' => static::getUnitsCompleted($userId),
        ];
    }
    
    /**
     * Get speaking stats
     * 
     * @param int $userId
     * 
     * @return array
     */ 
    public static function getSpeakingStat($userId)
    {
        return [
            'score' => static::getSpeakingQualityStat($userId),
            'sentences' => static::getSentencesSpoken($userId),
        ];
    }
    
    /**
     * Get overall speaking score
     * 
     * @param int $userId
     * 
     * @return float
     */ 
    public static function getSpeakingQualityStat($userId)
    {
        return (float) Progress\Activity::whereHas('progress', function($query) use($userId) {
            $query->where('user_id', '=', $userId);
        })
            ->avg('best_score');
    }
    
    /**
     * Get number of sentences that was spoken by user
     * 
     * @param int $userId
     * 
     * @return int
     */
    public static function getSentencesSpoken($userId)
    {
        return (int) Progress\ReadingReport::whereHas('progress', function($query) use($userId) {
            $query->where('user_id', '=', $userId);
        })
            ->where('is_actual', '=', 1)
            ->sum('practiced_sentences');
    }
    
    /**
     * Get quiz stats
     * 
     * @param int $userId
     * 
     * @return array
     */ 
    public static function getQuizStat($userId)
    {
        $result = static::where('user_id', '=', $userId)
            ->where('quiz_completed', '=', 1)
            ->getQuery()
            ->select(DB::raw('COUNT(*) AS total_count, AVG(quiz_score) AS avg_score'))
            ->first();
        return [
            'completed' => (int) $result->total_count,
            'avg_score' => (float) $result->avg_score,
        ];
    }
    
    /**
     * Get number of completed units
     * 
     * @param int $userId
     * 
     * @return int
     */ 
    public static function getUnitsCompleted($userId)
    {
        return (int) static::where('user_id', '=', $userId)
            ->whereNotNull('completed_at')
            ->count();
    }
    
    /**
     * Sync the progress
     * 
     * @param User $user
     * @param array $data
     */ 
    public static function sync($user, $units)
    {
        foreach ($units as $unitData) {
            try {
                $unitProgress = static::ofUnit($user->id, $unitData['id'])->first();
                if ($unitProgress === null) {
                    $unit = new Unit($unitData['id']);
                    $unitProgress = static::init($user, $unit);
                    $unitProgress->persist();
                }
                
                if (isset($unitData['activities'])) {
                    foreach ($unitData['activities'] as $activity) {
                        $report = Progress\ReadingReport::sync($unitProgress, $activity);
                        $unitProgress->updateFromReadingReport($report);
                    }
                }
                
                if (isset($unitData['quiz'])) {
                    $report = Progress\QuizReport::sync($unitProgress, $unitData['quiz']);
                    $unitProgress->updateFromQuizReport($report);
                }
            } catch (Exception $e) {
            }
        }
    }
}
