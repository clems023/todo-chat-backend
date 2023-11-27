<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    //Ajout de tâches à un projet
    public function createTask(Request $request, $projectId)
    {

        $validatedData = $request->validate([
            'description' => ['required', 'string']
        ]);

        try {

            $user = auth()->user();

            // Récupérer le projet associé
            $project = Project::find($projectId);
            // Vérifier si le projet existe
            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project missing',
                    'data' => []
                ]);
            }

            if ($user->id === $project->createdByUser->id) {
                // Créer une nouvelle tâche pour le projet
                $task = Task::create([
                    'description' => $validatedData['description'],
                    'project_id' => $project->id
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Task created successfully',
                    'data' => $task,
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "You don't have permission to access this project",
                    'data' => [],
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    //Modification d'une tâche dans un projet
    public function updateTask(Request $request, $projectId, $taskId)
    {
        $validatedData = $request->validate([
            'description' => ['sometimes', 'required', 'string'],
            'is_completed' => ['sometimes'],
        ]);

        try {
            $user = auth()->user();

            // Récupérer le projet associé
            $project = Project::find($projectId);

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project not found',
                    'data' => [],
                ], 404);
            }

            if ($user->id === $project->createdByUser->id) {
                // Récupérer la tâche
                $task = Task::where('project_id', $project->id)->find($taskId);
                if (!$task) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Task not found',
                        'data' => [],
                    ], 404);
                }

                // Mettre à jour la tâche en fonction des données validées
                $task->update($validatedData);

                return response()->json([
                    'success' => true,
                    'message' => 'Task updated successfully',
                    'data' => $task,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "You don't have permission to access this project",
                    'data' => [],
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 500);
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
                return response()->json([
                    'success' => false,
                    'message' => 'Project not found',
                    'data' => [],
                ], 404);
            }

            if ($user->id === $project->createdByUser->id) {
                // Récupérer et supprimer la tâche
                $task = Task::where('project_id', $project->id)->findOrFail($taskId);

                if (!$task) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Task not found',
                        'data' => [],
                    ], 404);
                }
                $task->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Task deleted successfully',
                    'data' => [],
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "You don't have permission to access this project",
                    'data' => [],
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 500);
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
                return response()->json([
                    'success' => false,
                    'message' => 'Project not found',
                    'data' => [],
                ], 404);
            }

            if ($user->id === $project->createdByUser->id) {
                // Récupérer les tâches du projet
                $tasks = Task::where('project_id', $project->id)->get();


                return response()->json([
                    'success' => true,
                    'message' => 'Tasks retrieved successfully',
                    'data' => $tasks,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "You don't have permission to access this project",
                    'data' => [],
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
