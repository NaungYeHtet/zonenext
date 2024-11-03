<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Viewable;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function property(Request $request, Property $property)
    {
        $viewExist = Viewable::where('viewer_id', $request->viewer_id)
            ->where('viewable_id', $property->id)
            ->where('viewable_type', $property->getMorphClass())
            ->where('ip_address', $request->ip())
            ->where('user_agent', $request->userAgent())
            ->first();

        if (! $viewExist) {
            $property->update([
                'views_count' => $property->views_count + 1,
            ]);

            Viewable::create([
                'viewer_id' => $request->viewer_id,
                'viewable_id' => $property->id,
                'viewable_type' => $property->getMorphClass(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $this->responseSuccess();
    }
}
