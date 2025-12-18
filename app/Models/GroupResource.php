<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class GroupResource extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'study_group_id',
        'uploaded_by',
        'title',
        'description',
        'type',
        'file_path',
        'file_type',
        'file_size_bytes',
        'link',
        'visibility',
    ];

    protected $casts = [
        'file_size_bytes' => 'integer',
    ];

    protected $appends = [
        'file_url',
    ];

    public const TYPE_FILE = 'file';
    public const TYPE_LINK = 'link';

    public const VISIBILITY_GROUP = 'group';
    public const VISIBILITY_SHARED = 'shared';

    public static function typeValues(): array
    {
        return [
            self::TYPE_FILE,
            self::TYPE_LINK,
        ];
    }

    public static function visibilityValues(): array
    {
        return [
            self::VISIBILITY_GROUP,
            self::VISIBILITY_SHARED,
        ];
    }

    public function group()
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileUrlAttribute(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        return Storage::disk('public')->url($this->file_path);
    }
}