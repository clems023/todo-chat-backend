<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use App\Models\Subtask;
use App\Models\ProjectUser;
use App\Traits\ApiHttpResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubtaskRequest;
use App\Http\Requests\UpdateSubtaskRequest;
use Exception;

class SubtasksController extends Controller
{
    use ApiHttpResponses;
    //

    public function addSubtask(StoreSubtaskRequest $request, $taskId)
    {
        $validatedData = $request->all();

        $task = Task::find($taskId);

        if (!$task) {
            return $this->sendErrors([], "Task not found", 404);
        }
        try {
            $user = auth()->user();

            $userProject = ProjectUser::where('project_id', $task->project->id)
                ->where('user_id', $user->id)
                ->first();

            if ($user->id === $task->project->created_by || $userProject) {
                $subtask = new Subtask($validatedData);
                $task->subtasks()->save($subtask);

                $results = ['subtasks' => $subtask];

                return $this->sendResponse($results, "Subtask added successfully", 201);
            }
            return $this->sendErrors([], "Something went wrong", 400);
        } catch (Exception $e) {
            return $this->sendErrors($e->getMessage(), "An error occurred while adding subtask", 500);
        }
    }

    public function updateSubtask(UpdateSubtaskRequest $request, $subtaskId)
    {
        $validatedData = $request->all();

        $subtask = Subtask::find($subtaskId);

        if (!$subtask) {
            return $this->sendErrors([], "Subtask not found", 404);
        }

        $user = auth()->user();

        $projectUser = $subtask->task->project->created_by;

        $userProject = ProjectUser::where('project_id', $subtask->task->project->id)
            ->where('user_id', $user->id)
            ->where('ability', "moderator")
            ->first();

        if ($user->id === $projectUser || $userProject) {
            $subtask->update($validatedData);
            $results = ['subtask' => $subtask];
            return $this->sendResponse($results, "Subtask updated successfully", 200);
        }
        return $this->sendErrors([], "You are unauthorized to perform this action", 403);

    }

    public function deleteSubtask($subtaskId)
    {
        $subtask = Subtask::find($subtaskId);

        if (!$subtask) {
            return $this->sendErrors([], "Subtask not found", 404);
        }


        $user = auth()->user();

        $projectUser = $subtask->task->project->created_by;

        $userProject = ProjectUser::where('project_id', $subtask->task->project->id)
            ->where('user_id', $user->id)
            ->where('ability', "moderator")
            ->first();

        if ($user->id === $projectUser || $userProject) {
            $subtask->delete();
            return $this->sendResponse([], "Subtask deleted successfully", 204);

        }
        return $this->sendErrors([], "You are unauthorized to perform this action", 403);

    }

    public function listOfSubtasks($taskId)
    {

        $task = Task::find($taskId);

        if (!$task) {
            return $this->sendErrors([], 'Task not found', 404);
        }


        $user = auth()->user();
        $projectUser = $task->project->created_by;

        $userProject = ProjectUser::where('project_id', $task->project->id)
            ->where('user_id', $user->id)
            ->first();

        if ($user->id === $projectUser || $userProject) {

            $subtasks = $task->subtasks;

            $results = ['data' => $subtasks];

            return $this->sendResponse($results, 'Subtasks fetched successfully', 200);
        }
        return $this->sendErrors([], "Unauthorised access", 401);
    }
}
