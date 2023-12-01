<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Traits\ApiHttpResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserAbilityRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Exception;
use Ramsey\Uuid\Uuid;

class ProjectsController extends Controller
{
    use ApiHttpResponses;
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
        $userProjects = ["projects" => $user->projects];

        return $this->sendResponse($userProjects, "Projects retrieved successfully", 200);
    }

    public function getProjectInvitedOn()
    {
        $user = auth()->user();

        $results = ["projects" => $user->projectBelongs];

        return $this->sendResponse($results, "Projects retrieved successfully", 200);
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
    public function storeUserProject(StoreProjectRequest $request)
    {
        $validatedData = $request->all();

        try {
            // Récupérer l'utilisateur connecté
            $user = auth()->user();

            // Créer un nouveau projet
            $project = Project::create([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'reference' => Uuid::uuid4(),
                'created_by' => $user->id,
            ]);

            // Charger les informations de l'utilisateur associé
            $project->load('createdByUser');

            return $this->sendResponse(["project" => $project], "Project created successfully", 200);

        } catch (Exception $e) {
            return $this->sendErrors($e->getMessage(), "Something went wrong", 500);
        }
    }

    //Update a project
    public function updateUserProject(UpdateProjectRequest $request, Project $project)
    {
        $validatedData = $request->all();

        try {
            // Récupérer l'utilisateur connecté
            $user = auth()->user();

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

            $results = [
                'project' => $project
            ];

            return $this->sendResponse($results, "Project created successfully", 201);
        } catch (Exception $e) {
            return $this->sendErrors($e->getMessage(), "Something went wrong", 500);
        }
    }

    // Supprimer un projet de l'utilisateur connecté
    public function deleteUserProject(Project $project)
    {
        try {
            // Récupérer l'utilisateur connecté
            $user = auth()->user();

            if ($user->id === $project->created_by) {
                return $this->sendErrors(null, "Project does not exists or you have not permission to delete it", 404);
            }

            // Supprimer le projet
            $project->delete();
            return $this->sendResponse(["project" => $project], "Project deleted successfully", 204);

        } catch (Exception $e) {
            return $this->sendErrors($e->getMessage(), "Something went wrong", 500);
        }
    }

    public function updateUserAbility(UserAbilityRequest $request, Project $project)
    {
        $validatedData = $request->all();
        $userToGrant = User::where("email", $validatedData['email'])->first();
        $user = auth()->user();

        try {
            if ($user->id === $project->created_by) {
                if (!$userToGrant) {
                    return $this->sendErrors([], "User not found", 404);
                }

                $projectUser = ProjectUser::where('user_id', $userToGrant->id)
                    ->where('project_id', $project->id)
                    ->first();

                if (!$projectUser) {
                    return $this->sendErrors([], "This user is not on your project", 400);
                }

                $projectUser->update(['ability' => $validatedData["ability"]]);

                return $this->sendResponse([], 'successfully updated', 200);


            }

            return $this->sendErrors([], "Access denied", 403);
        } catch (Exception $e) {
            return $this->sendErrors($e->getMessage(), "An error occurred while updating the ability of this user to access the project");
        }


    }



}

