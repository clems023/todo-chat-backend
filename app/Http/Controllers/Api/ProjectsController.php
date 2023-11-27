<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProjectsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/projects",
     *     summary="Retrieve user projects",
     *     description="Retrieves all projects associated with the logged-in user.",
     *     operationId="getUserProjects",
     *     tags={"Projects"},
     *     security={{"bearer": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Projects retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized access"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal Server Error"),
     *         ),
     *     ),
     * )
     */

    //Get all of project of the logged user
    public function getUserProjects()
    {
        // Récupérer l'utilisateur connecté
        $user = auth()->user();

        // Récupérer tous les projets associés à l'utilisateur
        $userProjects = $user->projects;

        return response()->json([
            'success' => true,
            'message' => 'Projects retrieved successfully',
            'data' => $userProjects,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/projects/create",
     *     summary="Create a new user project",
     *     description="Creates a new project associated with the logged-in user.",
     *     operationId="storeUserProject",
     *     tags={"Projects"},
     *     security={{"bearer": {}}},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Project name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         description="Descripton",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Project created successfully"),
     *
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="data", type="object", example="Validation error details"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized access"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal Server Error"),
     *         ),
     *     ),
     * )
     */

    //Store the projet of a user
    public function storeUserProject(Request $request)
    {

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:50'],
            'description' => ['nullable', 'string'],
        ]);

        $existingProjectName = Project::where('name', $validatedData['name'])->first();

        try {

            if ($existingProjectName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project already exists',
                    'data' => []
                ], 400);
            }
            // Récupérer l'utilisateur connecté
            $user = auth()->user();

            // Créer un nouveau projet
            $project = Project::create([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'created_by' => $user->id,
            ]);

            // Charger les informations de l'utilisateur associé
            $project->load('createdByUser');

            return response()->json([
                'success' => true,
                'message' => 'Project created successfully',
                'data' => $project,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    //Update a project
    public function updateUserProject(Request $request, $projectId)
    {
        $validatedData = $request->validate([
            'name' => ['nullable', 'string', 'min:2', 'max:50'],
            'description' => ['nullable', 'string'],
        ]);

        try {

            // Récupérer l'utilisateur connecté
            $user = auth()->user();

            // Récupérer le projet à mettre à jour
            $project = Project::where('id', $projectId)
                ->where('created_by', $user->id)
                ->first();

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project not found or you do not have permission to update it',
                    'data' => [],
                ], 404);
            }

            $existingProjectName = Project::where('name', $validatedData['name'])->first();

            if ($existingProjectName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project already exists',
                    'data' => []
                ], 400);
            }
            // Mettre à jour sélectivement les informations du projet
            if (isset($validatedData['name'])) {
                $project->update(['name' => $validatedData['name']]);
            }

            if (isset($validatedData['description'])) {
                $project->update(['description' => $validatedData['description']]);
            }

            // Enregistrer les modifications
            $project->save();

            // Recharger les informations du projet mis à jour
            $project->load('createdByUser');

            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully',
                'data' => $project,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    // Supprimer un projet de l'utilisateur connecté
    public function deleteUserProject($projectId)
    {
        try {
            // Récupérer l'utilisateur connecté
            $user = auth()->user();

            // Récupérer le projet à supprimer
            $project = Project::where('id', $projectId)
                ->where('created_by', $user->id)
                ->first();

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project not found or you do not have permission to delete it',
                    'data' => [],
                ], 404);
            }

            // Supprimer le projet
            $project->delete();

            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully',
                'data' => [],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }



}

