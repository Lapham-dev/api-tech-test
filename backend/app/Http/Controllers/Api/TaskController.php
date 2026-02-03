<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // GET /api/tasks
    public function index()
    {
        return response()->json(Task::all());
    }

    // POST /api/tasks
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'done'         => 'boolean',
            'status'       => 'string|max:50',
            'priority'     => 'string|max:50',
            'due_date'     => 'nullable|date',
            'assigned_to'  => 'nullable|string|max:255',
        ]);

        $task = Task::create($data);

        return response()->json($task, 201);
    }

    // GET /api/tasks/{id}
    public function show($id)
    {
        return response()->json(Task::findOrFail($id));
    }

    // PUT /api/tasks/{id}
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $task->update($request->all());

        return response()->json($task);
    }

    // DELETE /api/tasks/{id}
    public function destroy($id)
    {
        Task::destroy($id);

        return response()->json(null, 204);
    }
}

