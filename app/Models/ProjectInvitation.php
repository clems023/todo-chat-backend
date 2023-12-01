<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        "project_id",
        "inviter_user_id",
        "invited_user_id",
        "is_active",
        "created_at",
    ];

    protected $hidden = [
        "updated_at",
        "inviter_user_id",
        "invited_user_id",
    ];
}
