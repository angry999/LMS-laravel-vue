<?php

namespace App\Unit\GuidedSpeechActivity;

use SimpleXMLElement;
use App\JsonObject;
use App\Unit;

class GuidedSpeech extends JsonObject
{
    /**
     * @var Unit
     */
    protected $unit;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $video;

    /**
     * @var string
     */
    protected $sentenceStringId;

    /**
     * @var string
     */
    protected $answerStringId;

    /**
     * @var string
     */
    protected $startSnapshot;

    /**
     * @var string
     */
    protected $endSnapshot;

    /**
     * @var string
     */
    protected $guide;

    /**
     * @var string
     */
    protected $exampleAnswerVideo;

    /**
     * @param Unit $unit
     * @param SimpleXMLElement $data
     */
    public function __construct(Unit $unit, SimpleXMLElement $data)
    {
        $this->unit = $unit;
        $this->id = (string) $data['id'];
        $this->video = (string) $data['video'];

        $this->text = $this->unit->getString((string) $data['sentenceStringId']);
        $this->start_snapshot = (string) $data['startSnapshot'];
        $this->end_snapshot = (string) $data['endSnapshot'];
        $this->sentence_id = (int) $data['sentenceStringId'];

        $this->guide = (string) $data['guide'];
        $this->answer_id = (string) $data['answerStringId'];
        $this->example_answer_video = (string) $data['exampleAnswerVideo'];
    }

    /**
     * Convert the word instance to an array
     *
     * @return array
     */
    public function toArray()
    {
        return array_filter([
            'id' => $this->id,
            'node_id' => $this->id,
            'video' => $this->video,
            'text' => $this->text,
            'start_snapshot' => $this->start_snapshot,
            'end_snapshot' => $this->end_snapshot,
            'sentence_id' => $this->sentence_id,
            'guide' => $this->guide,
            'answer_id' => $this->answer_id,
            'example_answer_video' => $this->example_answer_video 
        ]);
    }
}
