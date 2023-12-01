<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Models\TasksUser;
use App\Models\ProjectUser;
use App\Traits\ApiHttpResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\AssignTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TasksController extends Controller
{
    use ApiHttpResponses;
    //Ajout de tâches à un projet
    public function createTask(StoreTaskRequest $request, $projectId)
    {

        try {
            $validatedData = $request->all();

            $user = auth()->user();

            // Récupérer le projet associé
            $project = Project::find($projectId);
            // Vérifier si le projet existe
            if (!$project) {
                return $this->sendErrors([], "Project not found", 404);
            }

            $userProject = ProjectUser::where('project_id', $project->id)
                ->where('user_id', $user->id)
                ->where('ability', "moderator")
                ->first();

            if ($user->id === $project->createdByUser->id || $userProject) {
                // Créer une nouvelle tâche pour le projet
                $task = Task::create([
                    'description' => $validatedData['description'],
                    'project_id' => $project->id,
                    'due_date' => $validatedData['due_date'] ?? null,
                    'priority' => $validatedData['priority'] ?? null,
                ]);

                return $this->sendResponse([], "Task created successfully", 201);
            }

            return $this->sendErrors([], "You don't have permission to access this project", 403);

        } catch (Exception $e) {

            return $this->sendErrors($e->getMessage(), "Something went wrong", 500);
        }
    }

    //Modification d'une tâche dans un projet
    public function updateTask(UpdateTaskRequest $request, $projectId, $taskId)
    {
        try {
            $validatedData = $request->all();
            $user = auth()->user();

            // Récupérer le projet associé
            $project = Project::find($projectId);

            if (!$project) {
                return $this->sendErrors([], "Project not found", 404);
            }

            $userProject = ProjectUser::where('project_id', $project->id)
                ->where('user_id', $user->id)
                ->where('ability', "moderator")
                ->first();

            if ($user->id === $project->createdByUser->id || $userProject) {
                // Récupérer la tâche
                $task = Task::where('project_id', $project->id)->find($taskId);
                if (!$task) {
                    return $this->sendErrors([], "Task not found", 404);
                }

                // Mettre à jour la tâche en fonction des données validées
                $task->update($validatedData);

                return $this->sendResponse([], "Task updated successfully");
            } else {
                return $this->sendErrors([], "You don't have permission to access this project", 403);
            }
        } catch (Exception $e) {
            return $this->sendErrors($e->getMessage(), "Something went wrong", 500);
        }
    }


    // Supprimer une tâche
    public function deleteTask($projectId, $taskId)
    {
        try {
            $user = auth()->user();

            // Récupérer le projet associé
            $project = Project::find($projectId);

            if (!$project) {
                return $this->sendErrors([], "Project not found", 404);
            }

            $userProject = ProjectUser::where('project_id', $project->id)
                ->where('user_id', $user->id)
                ->where('ability', "moderator")
                ->first();

            if ($user->id === $project->createdByUser->id || $userProject) {
                // Récupérer et supprimer la tâche
                $task = Task::where('project_id', $project->id)->findOrFail($taskId);

                if (!$task) {
                    return $this->sendErrors([], "Task not found", 404);
                }
                $task->delete();
                return $this->sendResponse([], "Task deleted successfully");
            } else {
                return $this->sendErrors([], "You don't have permission to access this project", 403);
            }
        } catch (Exception $e) {
            return $this->sendErrors($e->getMessage(), "Something went wrong", 500);
        }
    }

    // Liste des tâches d'un projet
    public function getProjectTasks($projectId)
    {
        try {
            $user = auth()->user();

            // Récupérer le projet associé
            $project = Project::find($projectId);

            if (!$project) {
                return $this->sendErrors([], "Project not found", 404);
            }

            $userProject = ProjectUser::where('project_id', $project->id)
                ->where('user_id', $user->id)
                ->first();

            if ($user->id === $project->createdByUser->id || $userProject) {
                // Récupérer les tâches du projet
                $tasks = Task::where('project_id', $project->id)->get();

                $results = ['tasks' => $tasks];

                return $this->sendResponse($results, "Tasks retrieved successfully", 200);
            } else {
                return $this->sendErrors([], "You don't have permission to access this project", 403);
            }
        } catch (Exception $e) {
            return $this->sendErrors($e->getMessage(), "Something went wrong", 500);
        }
    }

    public function assignUser(Task $task, AssignTaskRequest $request)
    {

        try {
            $validatedData = $request->all();

            $userAssigned = User::where('email', $validatedData['email'])->first();
            if (!$userAssigned) {
                return $this->sendErrors([], "User not found", 404);
            }
            $user = auth()->user();

            $isUserOnProject = ProjectUser::where('project_id', $task->project->id)
                ->where('user_id', $userAssigned->id)
                ->exists();

            if (!$isUserOnProject) {
                return $this->sendErrors([], "This user isn't in your project", 404);
            }

            $userProject = ProjectUser::where('project_id', $task->project->id)
                ->where('user_id', $user->id)
                ->where('ability', "moderator")
                ->first();

            if ($user->id === $task->project->created_by || $userProject) {
                TasksUser::updateOrCreate([
                    'assignee' => $user->id,
                    'task_id' => $task->id,
                    'user_id' => $userAssigned->id
                ]);

                return $this->sendResponse([], "User assigned to the task successfully", 200);
            }
            return $this->sendErrors([], "You don't have permission to assign a user on this task", 403);
        } catch (Exception $e) {
            return $this->sendErrors($e->getMessage(), "Something went wrong", 500);
        }
    }
}
