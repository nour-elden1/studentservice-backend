<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Task;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = $request->user();

        $query = Task::where('user_id', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->get('priority'));
        }

        $tasks = $query
            ->orderByRaw("FIELD(status, 'in_progress','todo','done')")
            ->orderBy('due_date')
            ->get();

        return $this->successResponse('Tasks list', $tasks);
    }

    public function store(StoreTaskRequest $request)
    {
        $user = $request->user();

        $task = Task::create([
            'user_id' => $user->id,
            ...$request->validated(),
        ]);

        return $this->successResponse('Task created', $task, 201);
    }

    public function show(Request $request, Task $task)
    {
        $this->authorizeTask($request, $task);

        return $this->successResponse('Task details', $task);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorizeTask($request, $task);

        $task->update($request->validated());

        return $this->successResponse('Task updated', $task);
    }

    public function destroy(Request $request, Task $task)
    {
        $this->authorizeTask($request, $task);

        $task->delete();

        return $this->successResponse('Task deleted');
    }

    protected function authorizeTask(Request $request, Task $task): void
    {
        if ($task->user_id !== $request->user()->id) {
            abort(403, 'You are not allowed to access this task.');
        }
    }
}