<?php

namespace App\Http\Controllers;

use App\Enums\PropertyStatus;
use App\Http\Requests\IndexPropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Models\Property;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexPropertyRequest $request)
    {
        $properties = Property::posted()
            ->search($request->validated('search'))
            ->filterListType($request->validated('list_type'))
            ->filterPrice($request->validated('price_from'), $request->validated('price_to'))
            ->filterState($request->validated('state'), $request->validated('township'))
            ->filterType($request->validated('type'))
            ->filterTownship($request->validated('township'))
            ->paginate(10);

        return $this->responseSuccess([
            'properties' => PropertyResource::collection($properties),
            'meta' => [
                'hasMore' => $properties->hasMorePages(),
                'nextPage' => $properties->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        if ($property->status != PropertyStatus::Posted) {
            return $this->responseError(message: 'Property not found', status: 404);
        }

        return $this->responseSuccess([
            'property' => new PropertyResource($property),
        ]);
    }
}
