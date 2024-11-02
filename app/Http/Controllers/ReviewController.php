<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyReviewRequest;
use App\Models\Property;
use App\Models\Rateable;

class ReviewController extends Controller
{
    public function property(PropertyReviewRequest $request)
    {
        $property = Property::whereCode($request->code)->first();

        Rateable::create([
            'rateable_id' => $property->id,
            'rateable_type' => $property->getMorphClass(),
            'user_id' => $request->user()?->id,
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        // event(new \App\Events\PropertyRatingSubmitted($property, $request->user()?->id));

        return $this->responseSuccess([], __('rating.notification.submitted.title'));
    }
}
