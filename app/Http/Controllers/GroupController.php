<?php

namespace App\Http\Controllers;

use App\Enums\GroupType;
use App\Http\Requests\GroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;

class GroupController extends Controller
{
    public function __invoke(GroupRequest $request)
    {
        $groupType = GroupType::from($request->type);

        return $this->responseSuccess([
            'group' => new GroupResource(Group::filterType($groupType)->with($groupType->getGroupableRelationship())->first()),
        ]);
    }
}
