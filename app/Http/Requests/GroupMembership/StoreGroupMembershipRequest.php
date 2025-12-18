<?php

namespace App\Http\Requests\GroupMembership;

use App\Http\Requests\ApiRequest;
use App\Models\GroupMembership;
use Illuminate\Validation\Rule;

class StoreGroupMembershipRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role' => [
                'nullable',
                'string',
                Rule::in(GroupMembership::roleValues()),
            ],
        ];
    }
}