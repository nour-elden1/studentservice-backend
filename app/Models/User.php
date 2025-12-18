<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function studySessions()
    {
        return $this->hasMany(StudySession::class);
    }

    public function studyMaterials()
    {
        return $this->hasMany(StudyMaterial::class, 'uploaded_by');
    }

    public function createdStudyGroups()
    {
        return $this->hasMany(StudyGroup::class, 'created_by');
    }

    public function groupMemberships()
    {
        return $this->hasMany(GroupMembership::class);
    }

    public function studyGroups()
    {
        return $this->belongsToMany(
            StudyGroup::class,
            'group_memberships'
        )->withPivot(['role'])->withTimestamps();
    }

    public function groupResources()
    {
        return $this->hasMany(GroupResource::class, 'uploaded_by');
    }
}