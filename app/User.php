<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Masterfri\SmartRelations\SmartRelations;
use Carbon\Carbon;
use App\Unit\Progress;
use LMS;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, SmartRelations;
    
    const OPT_SUPPORTED_LANGUAGES = 'supported_languages';
    const OPT_DESIRED_LANGUAGE = 'desired_language';
    const DEFAULT_LANGUAGE = 'vi';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'session_id', 
        'session_expiration_date',
        'supported_languages',
    ];
    
    /**
     * @var array
     */ 
    protected $appends = [
        'name',
        'api_token',
    ];
    
    /**
     * @var array
     */ 
    protected $casts = [
        'options' => 'array',
    ];
    
    /**
     * @var array
     */ 
    protected $dates = [
        'session_expiration_date',
        'sync_at',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    
    /**
     * Get user name
     * 
     * @return string
     */ 
    public function getNameAttribute()
    {
        return $this->username;
    }
    
    /**
     * Get user supported languages
     * 
     * @return array
     */ 
    public function getSupportedLanguagesAttribute()
    {
        return $this->getOption(self::OPT_SUPPORTED_LANGUAGES, []);
    }
    
    /**
     * Set user supported languages
     * 
     * @param array $value
     */ 
    public function setSupportedLanguagesAttribute($value)
    {
        $this->setOption(self::OPT_SUPPORTED_LANGUAGES, $value);
    }
    
    /**
     * Get user desired language
     * 
     * @return string
     */ 
    public function getDesiredLanguageAttribute()
    {
        return $this->getOption(self::OPT_DESIRED_LANGUAGE, '');
    }
    
    /**
     * Set user desired language
     * 
     * @param string $value
     */ 
    public function setDesiredLanguageAttribute($value)
    {
        $this->setOption(self::OPT_DESIRED_LANGUAGE, $value);
    }
    
    /**
     * Generate SpeakingPal API token
     * 
     * @return string
     */ 
    public function getApiTokenAttribute()
    {
        $expire = sprintf('%sT23:59:59', Carbon::now()->addDay()->format('Y-m-d'));
        $secret = config('services.speakingpal.secret');
        $hash = hash_hmac('md5', sprintf('%s:%s:%s', $this->id, $this->session_id, $expire), $secret);
        return sprintf('%s:%s:%s:%s', $this->id, $this->session_id, $expire, $hash);
    }
    
    /**
     * Start new session. Creates new user, if user with specified username does not exist
     * 
     * @param array $session_data
     * 
     * @return User
     */ 
    public static function startSession($session_data)
    {
        $user = static::where('username', '=', $session_data['username'])->first();
        
        if ($user == null) {
            $user = new static();
        }
        
        $user->fill($session_data);
        
        if ($user->desired_language == '') {
            $user->desired_language = self::DEFAULT_LANGUAGE;
        }
        
        $user->save();
        
        return $user;
    }
    
    /**
     * User per unit progress 
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function progress()
    {
        return $this->hasMany(Progress::class, 'user_id');
    }
    
    /**
     * Get user progress on the particular unit
     * 
     * @param Unit $unit
     * 
     * @return Progress
     */ 
    public function getUnitProgress(Unit $unit)
    {
        $progress = Progress::ofUnit($this->id, $unit->id)->first();
        if ($progress === null) {
            $progress = Progress::init($this, $unit);
        }
        return $progress;
    }
    
    /**
     * Get user overall progress
     * 
     * @return array
     */ 
    public function getOverallProgress()
    {
        return [
            'per_unit' => $this->progress,
        ];
    }
    
    /**
     * Set user option
     * 
     * @param string $key
     * @param mixed $value
     */ 
    public function setOption($key, $value)
    {
        $options = is_array($this->options) ? $this->options : [];
        $options[$key] = $value;
        $this->options = $options;
    }
    
    /**
     * Get user option
     * 
     * @param string $key
     * @param mixed $default
     * 
     * @return mixed
     */ 
    public function getOption($key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }
    
    /**
     * Get overall user progress
     * 
     * @return array
     */ 
    public function getProgressStat()
    {
        return Progress::getStat($this->id);
    }
    
    /**
     * Sync user progress from the LMS service
     */ 
    public function syncProgress()
    {
        $since = $this->sync_at ? $this->sync_at->addMinute() : null;
        $missingProgress = LMS::getProgress($this->session_id, $since);
        Progress::sync($this, $missingProgress->getUnits());
        $this->setSynced();
    }
    
    /**
     * Mark user 
     */ 
    public function setSynced()
    {
        $this->sync_at = Carbon::now();
        $this->save();
    }
}
