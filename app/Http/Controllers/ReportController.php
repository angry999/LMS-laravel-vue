<?php

namespace App\Http\Controllers;

use App\Unit;
use App\Http\Requests\ReadingReportRequest;
use App\Http\Requests\QuizReportRequest;
use App\Unit\Progress\ReadingReport;
use App\Unit\Progress\SentenceReport;
use App\Unit\Progress\QuizReport;
use LMS;
use App\LMS\LmsException;
use Illuminate\Http\Request;

class ReportController extends ApiController
{
    public function readingReport(ReadingReportRequest $request, Unit $unit)
    {
        $user = $request->user();
        $activity_number = $request->input('number');
        $progress = $user->getUnitProgress($unit);
        $progress->persist();
        
        if ($progress->activityLocked($activity_number)) {
            abort(403);
        }
        
        $report = new ReadingReport();
        $report->progress = $progress;
        $report->activity_number = $activity_number;
        $report->sentences = $request->input('results');
        $report->save();
        $progress->updateFromReadingReport($report);
        
        try {
            LMS::reportReadingProgress($user->session_id, $report);
        } catch (LmsException $e) {
        }
        
        $user->setSynced();
        
        $unit->loadProgress($user);
        return response()->json([
            'progress' => $unit->progress,
        ]);
    }
    
    public function quizReport(QuizReportRequest $request, Unit $unit)
    {
        $user = $request->user();
        $progress = $user->getUnitProgress($unit);
        $progress->persist();
        
        if ($progress->quizLocked()) {
            abort(403);
        }
        
        $report = new QuizReport();
        $report->progress = $progress;
        $report->results = $request->input('results');
        $report->save();
        $progress->updateFromQuizReport($report);
        
        try {
            LMS::reportQuizProgress($user->session_id, $report);
        } catch (LmsException $e) {
        }
        
        $user->setSynced();
        
        $unit->loadProgress($user);
        return response()->json([
            'progress' => $unit->progress,
        ]);
    }
    
    public function getOverallProgress(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'progress' => $user->getProgressStat(),
        ]);
    }
}