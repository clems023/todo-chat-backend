<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Models\ProjectUser;
use App\Traits\ApiHttpResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\RemoveUserRequest;

class ProjectUserController extends Controller
{
    use ApiHttpResponses;
    public function removeUserFromProject(Project $project, RemoveUserRequest $request)
    {
        $user = auth()->user();

        $userEmail = $request->email;

        if ($user->id === $project->created_by) {
            $projectUser = ProjectUser::join('users', 'project_users.user_id', '=', 'users.id')
                ->where('project_users.project_id', $project->id)
                ->where('users.email', $userEmail)
                ->first();

            if (!$projectUser) {
                return $this->sendErrors([], "User not found", 404);
            }


            $projectUser->delete();

            return $this->sendResponse([], "User removed successfully", 200);
        }

        return $this->sendErrors([], "Access denied", 403);
    }

    public function listOfUsers(Project $project)
    {
        $user = auth()->user();

        if ($user->id === $project->created_by) {

            $users = $project->users;
            $results = ['users' => $users];
            return $this->sendResponse($results, "Users fetched successfully");
        }

        return $this->sendErrors([], "Access denied", 403);
    }


}
