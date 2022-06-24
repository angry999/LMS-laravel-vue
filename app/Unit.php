<?php

namespace App;

use Exception;
use Illuminate\Contracts\Routing\UrlRoutable;
use Carbon\Carbon;
use App\JsonObject;
use Cdn;

class Unit extends JsonObject implements UrlRoutable
{
    const FOLDER_UNITS = 'units';
    const FOLDER_TRANSLATIONS = 'translations';

    const BRANCH_ACTIVITY_COUNT = 8;
    const BRANCH_GUIDEDSPEECH_COUNT = 4;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $strings = [];

    /**
     * @param string $id
     */
    public function __construct($id=null)
    {
        if ($id !== null && !$this->load($id)) {
            throw new Exception('Unit ' . $id . ' does not exist');
        }
    }

    /**
     * Dynamically retrieve unit property
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Determine if unit property defined.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Convert the unit instance to an array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Get the value of the unit route key
     *
     * @return mixed
     */
    public function getRouteKey()
    {
        return $this->id;
    }

    /**
     * Get the route key for the unit
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Retrieve the unit for a bound value.
     *
     * @param  mixed  $value
     * @return Unit|null
     */
    public function resolveRouteBinding($value)
    {
        return $this->load($value) ? $this : null;
    }

    /**
     * Load XML file
     *
     * @param string $file_name
     * @param string $folder
     *
     * @return \SimpleXMLElement
     */
    protected function loadXml($file, $folder = self::FOLDER_UNITS)
    {
        $path = $folder . '/' . $this->id . '/' . $file;
        $xml = Cdn::getFile($path);
        libxml_use_internal_errors(true);
        $node = @simplexml_load_string($xml);
        if ($node === false) {
            throw new Exception('Error while loading xml: ' . implode(';', libxml_get_errors()));
        }
        return $node;
    }

    /**
     * Load book details
     *
     * @param string $id
     * @return bool
     */
    public function load($id)
    {
        $this->data = ['id' => $id];

        try {
            $data = $this->loadXml('UnitMetadata.xml');
            $this->data['name'] = (string) $data->unitName;
            $this->data['level'] = (string) $data->unitLevel;
            $this->data['image'] = (string) $data->thumbnailImage;
            $this->data['description'] = (string) $data->Description;
            $this->data['quizAvailable'] = 'true' === (string)$data->showQuiz;
        } catch (Exception $e) {
            throw new Exception('Unit ' . $id . ' has invalid format: ' . $e->getMessage());
        }

        return true;
    }

    /**
     * Load strings
     */
    public function loadStrings()
    {
        foreach ($this->loadTranslationElements('en') as $string) {
            $this->strings[(string) $string['id']] = (string) $string;
        }
    }

    /**
     * Load translations
     *
     * @param string $language
     */
    public function loadTranslations($language)
    {
        $translations = [];
        try {
            foreach ($this->loadTranslationElements($language) as $string) {
                $translations[(string) $string['id']] = (string) $string;
            }
        } catch (Exception $e) {
        }
        $this->data['translations'] = $translations;
    }

    /**
     * Load translation elements
     *
     * @param string $language
     *
     * @return \SimpleXMLElement
     */
    protected function loadTranslationElements($language)
    {
        try {
            $data = $this->loadXml('Strings-' . $language . '.xml', self::FOLDER_TRANSLATIONS);
            return $data->String;
        } catch (Exception $e) {
            throw new Exception('Error while loading translations file: ' . $e->getMessage());
        }
    }

    /**
     * Get a translation by ID
     *
     * @param string $id
     *
     * @return string
     */
    public function getString($id)
    {
        if (!array_key_exists($id, $this->strings)) {
            return sprintf('%s: string is missing', $id);
        }

        return $this->strings[$id];
    }

    /**
     * Load quiz
     */
    public function loadQuiz()
    {
        try {
            $data = $this->loadXml('Quiz.xml');
            $this->data['quiz'] = new Unit\Quiz($this, $data);
        } catch (Exception $e) {
            throw new Exception('Error while loading quiz file: ' . $e->getMessage());
        }
    }

    /**
     * Load listening
     */
    public function loadListening()
    {
        try {
            $data = $this->loadXml('Listen.xml');
            $this->data['listening'] = new Unit\Listening($this, $data);
        } catch (Exception $e) {
            throw new Exception('Error while loading listening file: ' . $e->getMessage());
        }
    }

    /**
     * Load vocabulary
     */
    public function loadVocabulary()
    {
        try {
            $data = $this->loadXml('Vocabulary.xml');
            $this->data['vocabulary'] = new Unit\Vocabulary($this, $data);
        } catch (Exception $e) {
            throw new Exception('Error while loading vocabulary file: ' . $e->getMessage());
        }
    }

    /**
     * Load dialog
     */
    public function loadDialog()
    {
        try {
            $data = $this->loadXml('InteractiveDialog.xml');
            if ($data->getName() == 'InteractiveDialogs') {
                $dialogs = collect([]);
                foreach ($data->InteractiveDialog as $dialog) {
                    $dialogs->push(new Unit\Dialog($this, $dialog));
                }
                $this->data['dialogs'] = $dialogs;
            } else {
                $this->data['dialog'] = new Unit\Dialog($this, $data);
            }
        } catch (Exception $e) {
            throw new Exception('Error while loading vocabulary file: ' . $e->getMessage());
        }
    }

    /**
     * Load loadGuidedSpeech
     */
    public function loadGuidedSpeech()
    {
        try {
            $data = $this->loadXml('InteractiveDialog.xml');
            
            if ($data->getName() == 'InteractiveDialogs') {
                $guidedspeeches = collect([]);
                foreach ($data->GuidedSpeechActivity as $guidedspeech) {
                    $guidedspeeches->push(new Unit\GuidedSpeechActivity($this, $guidedspeech));
                }
                $this->data['guidedspeeches'] = $guidedspeeches;
            } else {
                $this->data['guidedspeeches'] = new Unit\GuidedSpeechActivity($this, $data);
            }

            
        } catch (Exception $e) {
            throw new Exception('Error while loading GuidedSpeechActivity file: ' . $e->getMessage());
        }
    }

    /**
     * Load all content
     */
    public function loadContent()
    {
        $this->loadStrings();
        if ($this->hasQuiz()) {
            $this->loadQuiz();
        }
        $this->loadListening();
        $this->loadVocabulary();
        $this->loadDialog();
        $this->countActivities();
    }

    /**
     * Load all content
     */
    public function loadContentForGuidedSpeech()
    {
        $this->loadStrings();
        if ($this->hasQuiz()) {
            $this->loadQuiz();
        }
        $this->loadListening();
        $this->loadVocabulary();
        $this->loadDialog();
        $this->countActivitiesForGuidedSpeech();
        $this->loadGuidedSpeech();
        $this->countGuidedSpeeches(); 
    }

    /**
     * Load user progress for this unit
     *
     * @param User $user
     */
    public function loadProgress(User $user)
    {
        $progress = $user->getUnitProgress($this);
        $progress->load('activities');
        $this->data['progress'] = $progress;
    }

    /**
     * Get activities count for the unit
     *
     * @return int
     */
    public function countActivities()
    {
        if (!isset($this->dialog) && !isset($this->dialogs)) {
            $this->loadDialog();
        }
        if (isset($this->dialogs)) {
            $this->data['activities_count'] = $this->dialogs->count();
        } else {
            $this->data['activities_count'] = self::BRANCH_ACTIVITY_COUNT;
        }
        return $this->data['activities_count'];
    }

    /**
     * Get activities count for the unit
     *
     * @return int
     */
    public function countActivitiesForGuidedSpeech()
    {
        if (!isset($this->dialog) && !isset($this->dialogs)) {
            $this->loadDialog();
        }
        if (isset($this->dialogs)) {
            $this->data['activities_count'] = $this->dialogs->count();
        } else {
            $this->data['activities_count'] = self::BRANCH_GUIDEDSPEECH_COUNT;
        }
        return $this->data['activities_count'];
    }

    /**
     * Get activities count for the unit
     *
     * @return int
     */
    public function countGuidedSpeeches()
    {
        if (!isset($this->guidedspeech) && !isset($this->guidedspeeches)) {
            $this->loadGuidedSpeech();
        }
        if (isset($this->guidedspeeches)) {
            $this->data['guidedspeeches_count'] = $this->guidedspeeches->count();
        } else {
            $this->data['guidedspeeches_count'] = self::BRANCH_GUIDEDSPEECH_COUNT;
        }
        return $this->data['guidedspeeches_count'];
    }

    /**
     * Check if this unit has quiz
     *
     * @return bool
     */
    public function hasQuiz()
    {
        return $this->data['quizAvailable'] ?? false;
    }
}
