<?php

namespace App\Http\Controllers;

use App\Enums\Filters\FilterListType;
use App\Enums\Filters\FilterPrice;
use App\Enums\PropertyType;
use App\Http\Requests\TownshipRequest;
use App\Http\Resources\StateOptionResource;
use App\Http\Resources\TownshipOptionResource;
use App\Models\State;
use App\Models\Township;

class PropertyFilterController extends Controller
{
    public function index()
    {
        return $this->responseSuccess([
            'list_types' => FilterListType::getOptions(),
            'states' => StateOptionResource::collection(State::all()),
            'type' => PropertyType::getOptions(),
            'price_ranges' => FilterPrice::getRangeOptions(),
        ]);
    }

    public function townships(TownshipRequest $request)
    {
        $townships = Township::search($request->validated('search'))
            ->filterState($request->validated('state'))
            ->paginate(10);

        return $this->responseSuccess([
            'townships' => TownshipOptionResource::collection($townships),
            'meta' => [
                'hasMore' => $townships->hasMorePages(),
                'nextPage' => $townships->nextPageUrl(),
            ],
        ]);
    }
}
