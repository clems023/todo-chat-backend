<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use App\Models\Subtask;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubtasksController extends Controller
{
    //

    public function addSubtask(Request $request, $taskId)
    {
        $validatedData = $request->validate([
            'description' => ['required', 'string']
        ]);

        $task = Task::find($taskId);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found',
                'data' => []
            ], 404);
        }

        $user = auth()->user();

        if ($user->id === $task->project->created_by) {
            $subtask = new Subtask($validatedData);
            $task->subtasks()->save($subtask);


            return response()->json([
                'success' => true,
                'message' => 'Subtask added successfully',
                'data' => ['subtasks' => $subtask]
            ], 201);
        }


        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'data' => []
        ], 403);


    }

    public function updateSubtask(Request $request, $subtaskId)
    {
        $validatedData = $request->validate([
            'description' => ['sometimes', 'string'],
            'completed' => ['sometimes', 'boolean']

        ]);

        $subtask = Subtask::find($subtaskId);

        if ($subtask) {
            return response()->json([
                'success' => false,
                'message' => 'Subtask not found',
                'data' => []
            ], 404);
        }

        $user = auth()->user();

        $projectUser = $subtask->task->project->created_by;

        if ($user->id === $projectUser) {
            $subtask->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Subtask modified successfully',
                'data' => $subtask
            ], 200);

        }

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'data' => []
        ], 403);

    }

    public function deleteSubtask($subtaskId)
    {
        $subtask = Subtask::find($subtaskId);

        if ($subtask) {
            return response()->json([
                'success' => false,
                'message' => 'Subtask not found',
                'data' => []
            ], 404);
        }


        $user = auth()->user();

        $projectUser = $subtask->task->project->created_by;

        if ($user->id === $projectUser) {
            $subtask->delete();

            return response()->json([
                'success' => true,
                'message' => 'Subtask deleted successfully',
                'data' => []
            ], 200);

        }

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'data' => []
        ], 403);

    }

    public function listOfSubtasks($taskId)
    {

        $task = Task::find($taskId);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found',
                'data' => []
            ], 404);
        }


        $user = auth()->user();
        $projectUser = $task->project->created_by;

        if ($user->id === $projectUser) {

            $subtasks = $task->subtasks;
            return response()->json([
                'success' => true,
                'message' => 'Subtasks fetched successfully',
                'data' => $subtasks
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'data' => []
        ], 403);

    }
}
