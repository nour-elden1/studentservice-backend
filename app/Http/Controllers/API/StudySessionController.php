<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudySession\StoreStudySessionRequest;
use App\Http\Requests\StudySession\UpdateStudySessionRequest;
use App\Models\StudySession;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class StudySessionController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = $request->user();

        $sessions = StudySession::where('user_id', $user->id)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return $this->successResponse('Study sessions list', $sessions);
    }

    public function store(StoreStudySessionRequest $request)
    {
        $user = $request->user();

        $session = StudySession::create([
            'user_id' => $user->id,
            ...$request->validated(),
        ]);

        return $this->successResponse('Study session created', $session, 201);
    }

    public function show(Request $request, StudySession $studySession)
    {
        $this->authorizeSession($request, $studySession);

        return $this->successResponse('Study session details', $studySession);
    }

    public function update(UpdateStudySessionRequest $request, StudySession $studySession)
    {
        $this->authorizeSession($request, $studySession);

        $studySession->update($request->validated());

        return $this->successResponse('Study session updated', $studySession);
    }

    public function destroy(Request $request, StudySession $studySession)
    {
        $this->authorizeSession($request, $studySession);

        $studySession->delete();

        return $this->successResponse('Study session deleted');
    }

    protected function authorizeSession(Request $request, StudySession $session): void
    {
        if ($session->user_id !== $request->user()->id) {
            abort(403, 'You are not allowed to access this study session.');
        }
    }
}