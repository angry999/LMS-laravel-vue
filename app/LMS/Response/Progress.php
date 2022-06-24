<?php 

namespace App\LMS\Response;

use App\LMS\LmsException;

class Progress extends BaseResponse
{
    protected $export = [
        'units',
    ];
    
    public function getUnits()
    {
        $result = collect([]);
        foreach ($this->result->Unit as $unit) {
            $result->push($this->getUnitProgress($unit));
        }
        return $result;
    }
    
    protected function getUnitProgress($unit)
    {
        $activities = collect([]);
        $quiz = null;
        foreach ($unit->Branch as $branch) {
            $activities->push([
                'id' => (int) $branch['id'],
                'grade' => (float) $branch['grade'],
                'practiced_sentences' => (int) $branch['practiced_sentences'],
            ]);
        }
        foreach ($unit->Quiz as $quiz) {
            $quiz = [
                'total' => (int) $quiz['total'],
                'correct' => (int) $quiz['correct'],
            ];
            break;
        }
        return [
            'id' => (string) $unit['id'],
            'activities' => $activities,
            'quiz' => $quiz,
        ];
    }
}