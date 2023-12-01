<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'reference',
        'description',
        'created_by',
        'created_at',
    ];

    protected $hidden = [
        'deleted_at',
        'created_by',
        'updated_at'
    ];

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_users');
    }

    public function projectUsers()
    {
        return $this->hasMany(ProjectUser::class);
    }
}
