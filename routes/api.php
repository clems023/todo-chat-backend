<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\ProjectsController;
use App\Http\Controllers\Api\RegistrationController;
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

Route::post('/register', [RegistrationController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {


    //Projects
    Route::get('/projects', [ProjectsController::class, 'getUserProjects']);
    Route::post('/projects/create', [ProjectsController::class, 'storeUserProject']);
    Route::put('/projects/update/{projectId}', [ProjectsController::class, 'updateUserProject']);
    Route::delete('/projects/delete/{projectId}', [ProjectsController::class, 'deleteUserProject']);

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

});


