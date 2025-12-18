<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudyGroup extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'created_by',
        'name',
        'description',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function memberships()
    {
        return $this->hasMany(GroupMembership::class);
    }

    public function members()
    {
        return $this->belongsToMany(
            User::class,
            'group_memberships'
        )->withPivot(['role'])->withTimestamps();
    }

    public function resources()
    {
        return $this->hasMany(GroupResource::class);
    }
}