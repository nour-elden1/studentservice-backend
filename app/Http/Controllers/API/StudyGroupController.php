<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupMembership\StoreGroupMembershipRequest;
use App\Http\Requests\StudyGroup\StoreStudyGroupRequest;
use App\Http\Requests\StudyGroup\UpdateStudyGroupRequest;
use App\Models\GroupMembership;
use App\Models\StudyGroup;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class StudyGroupController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = $request->user();

        $groups = StudyGroup::query()
            ->with(['members:id,name', 'creator:id,name'])
            ->whereHas('members', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->orderBy('name')
            ->get();

        return $this->successResponse('Study groups list', $groups);
    }

    public function store(StoreStudyGroupRequest $request)
    {
        $user = $request->user();

        $group = StudyGroup::create([
            'created_by' => $user->id,
            ...$request->validated(),
        ]);

        GroupMembership::create([
            'study_group_id' => $group->id,
            'user_id' => $user->id,
            'role' => GroupMembership::ROLE_OWNER,
        ]);

        $group->load(['members:id,name', 'creator:id,name']);

        return $this->successResponse('Study group created', $group, 201);
    }

    public function show(Request $request, StudyGroup $studyGroup)
    {
        $this->authorizeGroupAccess($request, $studyGroup);

        $studyGroup->load(['members:id,name', 'creator:id,name']);

        return $this->successResponse('Study group details', $studyGroup);
    }

    public function update(UpdateStudyGroupRequest $request, StudyGroup $studyGroup)
    {
        $this->authorizeGroupOwnership($request, $studyGroup);

        $studyGroup->update($request->validated());

        $studyGroup->load(['members:id,name', 'creator:id,name']);

        return $this->successResponse('Study group updated', $studyGroup);
    }

    public function destroy(Request $request, StudyGroup $studyGroup)
    {
        $this->authorizeGroupOwnership($request, $studyGroup);

        $studyGroup->delete();

        return $this->successResponse('Study group deleted');
    }

    public function addMember(
        StoreGroupMembershipRequest $request,
        StudyGroup $studyGroup
    ) {
        $this->authorizeGroupOwnership($request, $studyGroup);

        $data = $request->validated();

        $membership = GroupMembership::updateOrCreate(
            [
                'study_group_id' => $studyGroup->id,
                'user_id' => $data['user_id'],
            ],
            [
                'role' => $data['role'] ?? GroupMembership::ROLE_MEMBER,
            ]
        );

        return $this->successResponse('Member added to group', $membership, 201);
    }

    public function removeMember(Request $request, StudyGroup $studyGroup, $userId)
{
    $this->authorizeGroupOwnership($request, $studyGroup);

    $membership = GroupMembership::where('study_group_id', $studyGroup->id)
        ->where('user_id', $userId)
        ->firstOrFail();

    if ($membership->role === GroupMembership::ROLE_OWNER) {
        return $this->errorResponse(
            'Cannot remove the owner from the group',
            null,
            422
        );
    }

    $membership->delete();

    return $this->successResponse('Member removed from group');
}



    protected function authorizeGroupAccess(Request $request, StudyGroup $group): void
    {
        $userId = $request->user()->id;

        $isMember = $group->members()
            ->where('users.id', $userId)
            ->exists();

        if (! $isMember) {
            abort(403, 'You are not a member of this group.');
        }
    }

    protected function authorizeGroupOwnership(Request $request, StudyGroup $group): void
    {
        $userId = $request->user()->id;

        $isOwner = $group->members()
            ->where('users.id', $userId)
            ->wherePivot('role', GroupMembership::ROLE_OWNER)
            ->exists();

        if (! $isOwner) {
            abort(403, 'Only group owners can perform this action.');
        }
    }
}