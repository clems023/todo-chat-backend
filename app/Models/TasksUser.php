<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TasksUser extends Model
{
    use HasFactory;

    protected $fillable = [
        "assignee",
        "task_id",
        "user_id"
    ];
}
