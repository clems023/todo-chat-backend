<?php

namespace App\Models;

use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subtask extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'completed',
        'task',
        'created'
    ];

    protected $hidden = [
        'project',
        'updated_at',
        'deleted_at'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

}
