<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // GET /api/tasks
    public function index()
    {
        return response()->json([
            'ok' => true,
            'data' => Task::orderByDesc('id')->get(),
        ]);
    }

    // POST /api/tasks
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $task = Task::create([
            'title' => $validated['title'],
            'done' => false,
        ]);

        return response()->json([
            'ok' => true,
            'data' => $task,
        ], 201);
    }

    // GET /api/tasks/{task}
    public function show(Task $task)
    {
        return response()->json([
            'ok' => true,
            'data' => $task,
        ]);
    }

    // PATCH/PUT /api/tasks/{task}
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'done'  => ['sometimes', 'boolean'],
        ]);

        $task->update($validated);

        return response()->json([
            'ok' => true,
            'data' => $task->fresh(),
        ]);
    }

    // DELETE /api/tasks/{task}
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json([
            'ok' => true,
            'deleted' => true,
        ]);
    }
}


