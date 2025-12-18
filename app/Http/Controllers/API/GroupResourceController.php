<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupResource\StoreGroupResourceRequest;
use App\Http\Requests\GroupResource\UpdateGroupResourceRequest;
use App\Models\GroupResource;
use App\Models\StudyGroup;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GroupResourceController extends Controller
{
    use ApiResponse;

    public function indexByGroup(Request $request, StudyGroup $studyGroup)
    {
        $this->authorizeGroupAccess($request, $studyGroup);

        $resources = GroupResource::where('study_group_id', $studyGroup->id)
            ->orderByDesc('created_at')
            ->get();

        return $this->successResponse('Group resources list', $resources);
    }

    public function indexShared(Request $request)
    {
        $user = $request->user();

        $groupIds = $user->studyGroups()->pluck('study_groups.id')->toArray();

        $resources = GroupResource::whereIn('study_group_id', $groupIds)
            ->where('visibility', GroupResource::VISIBILITY_SHARED)
            ->orderByDesc('created_at')
            ->get();

        return $this->successResponse('Shared resources list', $resources);
    }

    public function store(
        StoreGroupResourceRequest $request,
        StudyGroup $studyGroup
    ) {
        $this->authorizeGroupAccess($request, $studyGroup);

        $data = $request->validated();
        $user = $request->user();

        $filePath = null;
        $fileType = null;
        $fileSize = null;

        if (($data['type'] ?? null) === GroupResource::TYPE_FILE && $request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('group-resources', 'public');
            $fileType = $file->getClientOriginalExtension();
            $fileSize = $file->getSize();
        }

        $resource = GroupResource::create([
            'study_group_id' => $studyGroup->id,
            'uploaded_by' => $user->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'file_path' => $filePath,
            'file_type' => $fileType,
            'file_size_bytes' => $fileSize,
            'link' => $data['link'] ?? null,
            'visibility' => $data['visibility'] ?? GroupResource::VISIBILITY_GROUP,
        ]);

        return $this->successResponse('Group resource created', $resource, 201);
    }

    public function show(
        Request $request,
        StudyGroup $studyGroup,
        GroupResource $groupResource
    ) {
        $this->authorizeGroupAccess($request, $studyGroup);

        $this->ensureResourceBelongsToGroup($studyGroup, $groupResource);

        return $this->successResponse('Group resource details', $groupResource);
    }

    public function update(
        UpdateGroupResourceRequest $request,
        StudyGroup $studyGroup,
        GroupResource $groupResource
    ) {
        $this->authorizeGroupAccess($request, $studyGroup);
        $this->ensureResourceBelongsToGroup($studyGroup, $groupResource);

        $data = $request->validated();

        if ($request->hasFile('file')) {
            if ($groupResource->file_path) {
                Storage::disk('public')->delete($groupResource->file_path);
            }

            $file = $request->file('file');
            $filePath = $file->store('group-resources', 'public');

            $data['file_path'] = $filePath;
            $data['file_type'] = $file->getClientOriginalExtension();
            $data['file_size_bytes'] = $file->getSize();
        }

        $groupResource->update($data);

        return $this->successResponse('Group resource updated', $groupResource);
    }

    public function destroy(
        Request $request,
        StudyGroup $studyGroup,
        GroupResource $groupResource
    ) {
        $this->authorizeGroupAccess($request, $studyGroup);
        $this->ensureResourceBelongsToGroup($studyGroup, $groupResource);

        if ($groupResource->file_path) {
            Storage::disk('public')->delete($groupResource->file_path);
        }

        $groupResource->delete();

        return $this->successResponse('Group resource deleted');
    }

    protected function authorizeGroupAccess(Request $request, StudyGroup $group): void
    {
        $user = $request->user();

        $isMember = $group->members()
            ->where('users.id', $user->id)
            ->exists();

        if (! $isMember) {
            abort(403, 'You are not a member of this group.');
        }
    }

    protected function ensureResourceBelongsToGroup(
        StudyGroup $group,
        GroupResource $resource
    ): void {
        if ($resource->study_group_id !== $group->id) {
            abort(404);
        }
    }
}