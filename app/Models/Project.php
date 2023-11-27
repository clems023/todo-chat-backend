<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
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
}
