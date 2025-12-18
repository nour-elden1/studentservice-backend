<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_group_id',
        'user_id',
        'role',
    ];

    public const ROLE_OWNER = 'owner';
    public const ROLE_MEMBER = 'member';

    public static function roleValues(): array
    {
        return [
            self::ROLE_OWNER,
            self::ROLE_MEMBER,
        ];
    }

    public function group()
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}