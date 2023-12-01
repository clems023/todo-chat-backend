<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\ProjectInvitationsController;
use App\Http\Controllers\Api\ProjectsController;
use App\Http\Controllers\Api\ProjectUserController;
use App\Http\Controllers\Api\SubtasksController;
use App\Http\Controllers\Api\TasksController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register', [AuthenticationController::class, 'register']);
Route::post('/login', [AuthenticationController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {


    //Projects
    Route::get('/projects', [ProjectsController::class, 'getUserProjects']);
    Route::post('/projects/create', [ProjectsController::class, 'storeUserProject']);
    Route::put('/projects/update/{project}', [ProjectsController::class, 'updateUserProject']);
    Route::delete('/projects/delete/{projectId}', [ProjectsController::class, 'deleteUserProject']);
    Route::delete('/projects/remove/{project}', [ProjectUserController::class, 'removeUserFromProject']);
    Route::get('projects/users/{project}', [ProjectUserController::class, 'listOfUsers']);
    Route::get('/projects/guest', [ProjectsController::class, 'getProjectInvitedOn']);
    Route::post('/projects/permissions/{project}', [ProjectsController::class, 'updateUserAbility']);

    //Tasks
    Route::get('/project/{projectId}/tasks', [TasksController::class, 'getProjectTasks']);
    Route::post('/tasks/{projectId}/create', [TasksController::class, 'createTask']);
    Route::put('/tasks/{projectId}/update/{taskId}', [TasksController::class, 'updateTask']);
    Route::delete('/tasks/{projectId}/delete/{taskId}', [TasksController::class, 'deleteTask']);

    //Subtasks
    Route::get('/task/{taskId}/subtasks', [SubtasksController::class, 'listOfSubtasks']);
    Route::post('/subtask/{taskId}/create', [SubtasksController::class, 'addSubtask']);
    Route::put('/subtask/update/{subtaskId}', [SubtasksController::class, 'updateSubtask']);
    Route::delete('/subtask/delete/{subtaskId}', [SubtasksController::class, 'deleteSubtask']);
    Route::post('/assign/{task}', [TasksController::class, 'assignUser']);

    //User invitation
    Route::post('/invitations/invite/{project}', [ProjectInvitationsController::class, 'sendInvitations']);
    Route::get('/invitations', [ProjectInvitationsController::class, 'getInvitations']);
    Route::post('/invitations/{projectInvitation}/accept', [ProjectInvitationsController::class, 'accept']);
    Route::post('/invitations/{projectInvitation}/decline', [ProjectInvitationsController::class, 'decline']);
});


