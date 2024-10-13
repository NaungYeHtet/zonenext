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
            'types' => PropertyType::getOptions(),
            'for_sale_options' => FilterPrice::getRangeOptions(FilterListType::ForSale),
            'for_rent_options' => FilterPrice::getRangeOptions(FilterListType::ForRent),
            'newest_options' => FilterPrice::getRangeOptions(FilterListType::Newest),
        ]);
    }

    public function townships(TownshipRequest $request)
    {
        $townships = Township::search($request->validated('search'))
            ->filterSlug($request->validated('slug'))
            ->filterState($request->validated('state'))
            ->paginate(10);

        return $this->responseSuccess([
            'townships' => TownshipOptionResource::collection($townships),
            'meta' => [
                'has_more' => $townships->hasMorePages(),
                'next_page' => $townships->nextPageUrl(),
            ],
        ]);
    }
}
