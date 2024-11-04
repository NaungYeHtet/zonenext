<?php

namespace App\Http\Controllers;

use App\Http\Resources\AgentResource;
use App\Models\Admin;

class AgentController extends Controller
{
    public function index()
    {
        $agents = Admin::agent();

        return $this->responseSuccess([
            'agents' => AgentResource::collection($agents->paginate(9)->setPath('/agents/'))->resource,
        ]);
    }
}
