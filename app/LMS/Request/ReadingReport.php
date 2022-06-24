<?php

namespace App\LMS\Request;

use App\Unit\Progress\ReadingReport as Report;

class ReadingReport extends AbstractRequest
{
    const P_SESSION_ID = 'session_id';
    const P_CATALOG_V3 = 'catalog_v3';
    const P_REPORT = 'report';
    const P_BRANCH_ID = 'branch_id';
    const P_UNIT_ID = 'unit_id';
    const P_BRANCH_GRADE = 'branch_grade';
    const P_CLIENT_SESSION_BRANCH = 'client_session_branch';
    const P_NODE_ID = 'node_id';
    const P_GRADE = 'grade';
    const P_TYPE = 'type';
    const P_TIMESTAMP = 'timestamp';
    const P_PRACTICED_SENTENCES = 'practiced_sentences';
    const P_ASR_SESSION_ID = 'asr_session_id';
    
    const TYPE_BRANCH = 'branch';
    
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
            self::P_BRANCH_ID => $report->activity_number,
            self::P_UNIT_ID => $report->unit_id,
            self::P_BRANCH_GRADE => $report->average_score,
            self::P_CLIENT_SESSION_BRANCH => $report->sentences->map(function($sentence) {
                return [
                    self::P_NODE_ID => $sentence->node_id,
                    self::P_GRADE => $sentence->grade,
                ];
            }),
            self::P_TYPE => self::TYPE_BRANCH,
            self::P_TIMESTAMP => $this->formatTimestamp($report->created_at),
            self::P_PRACTICED_SENTENCES => $report->practiced_sentences,
            self::P_ASR_SESSION_ID => $report->asr_session_id,
        ];
    }
}