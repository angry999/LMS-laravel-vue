<?php

namespace App\LMS\Request;

use App\Unit\Progress\QuizReport as Report;

class QuizReport extends AbstractRequest
{
    const P_SESSION_ID = 'session_id';
    const P_CATALOG_V3 = 'catalog_v3';
    const P_REPORT = 'report';
    const P_UNIT_ID = 'unit_id';
    const P_TIMESTAMP = 'timestamp';
    const P_TYPE = 'type';
    const P_CORRECT_ANSWERS = 'correct_answers';
    const P_QUESTIONS_ANSWERED = 'questions_answered';
    
    public function __construct($client, $session_id, Report $report)
    {
        parent::__construct($client);
        
        $this->setQuery([
            self::P_SESSION_ID => $session_id,
            self::P_CATALOG_V3 => 'true',
        ]);
        
        $this->setBody([
            self::P_REPORT => json_encode([$this->buildReportBody($report)]),
        ]);
    }
    
    public function getServiceName()
    {
        return 'ClientProgressReportV3';
    }
    
    protected function buildReportBody(Report $report)
    {
        return [
            self::P_UNIT_ID => $report->unit_id,
            self::P_CORRECT_ANSWERS => $report->correct,
            self::P_QUESTIONS_ANSWERED => $report->total,
            self::P_TYPE => 'quiz',
            self::P_TIMESTAMP => $this->formatTimestamp($report->created_at),
        ];
    }
}