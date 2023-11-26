<?php

// app/Http/Controllers/TaskController.php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::orderBy('created_at', 'desc')->get();

        return response()->json($tasks);
    }

    public function completed()
    {
        $tasks = Task::where('completed', 1)->orderBy('created_at', 'desc')->get();

        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|max:100',
            ]);

            $task = Task::create($validatedData);

            return response()->json($task, 201);
        } catch (\Exception $exception) {
            // Log the exception for debugging
            Log::error('Exception Message: ' . $exception->getMessage());
            Log::error('Exception Trace: ' . $exception->getTraceAsString());

            return response()->json($exception->getMessage(), 500);
        }
    }


    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'nullable|max:100',
            'completed' => 'boolean',
        ]);

        $task->update($request->all());

        return response()->json($task, 200);
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(null, 204);
    }

    public function updateCompletionStatus(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'completed' => 'required|boolean',
        ]);

        // Find the task by ID
        $task = Task::findOrFail($id);

        // Update the completion status
        $task->update([
            'completed' => $request->input('completed'),
        ]);

        // You can return a response if needed
        return response()->json(['message' => 'Task completion status updated successfully']);
    }
}
