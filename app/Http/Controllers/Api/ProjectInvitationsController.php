<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Traits\ApiHttpResponses;
use App\Models\ProjectInvitation;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvitationRequest;

class ProjectInvitationsController extends Controller
{

    use ApiHttpResponses;
    //

    public function sendInvitations(Project $project, StoreInvitationRequest $request)
    {
        $user = auth()->user();
        $validatedData = $request->all();

        if ($user->id === $project->created_by) {

            $invitedUser = User::where('email', $validatedData['email'])->first();

            if (!$invitedUser) {
                return $this->sendErrors([], "This user does not exist", 400);
            }

            $projectInv = ProjectInvitation::create([
                'project_id' => $project->id,
                'inviter_user_id' => $user->id,
                'invited_user_id' => $invitedUser->id
            ]);

            return $this->sendResponse($projectInv, "Invitation sent successfully", 200);
        }

        return $this->sendErrors([], "Access denied", 403);
    }

    public function getInvitations()
    {

        $user = auth()->user();

        $results = ['invitations' => $user->receivedInvitations];

        return $this->sendResponse($results, "Invitations retrieved successfully", 200);
    }

    public function accept(ProjectInvitation $projectInvitation)
    {

        $user = auth()->user();

        if ($user->id === $projectInvitation->invited_user_id && $projectInvitation->is_active !== 0) {
            $projectInvitation->update(['is_active' => 0]);

            ProjectUser::updateOrCreate(['project_id' => $projectInvitation->project_id, 'user_id' => $user->id]);

            return $this->sendResponse([], "User invited", 200);
        }

        return $this->sendErrors([], "Access denied", 403);
    }

    public function decline(ProjectInvitation $projectInvitation)
    {
        $user = auth()->user();

        if ($user->id === $projectInvitation->invited_user_id && $projectInvitation->is_active !== 0) {

            $projectInvitation->update(['is_active' => false]);

            return $this->sendResponse([], "Declined invitation", 200);
        }

        return $this->sendErrors([], "Access denied", 403);
    }
}
