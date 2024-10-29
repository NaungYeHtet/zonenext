<?php

namespace App\Http\Controllers;

use App\Enums\Lead\LeadContactMethod;
use App\Enums\Lead\LeadContactTime;
use App\Enums\Lead\LeadInterest;
use App\Enums\PropertyType;
use App\Http\Requests\InquiryRequest;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InquiryController extends Controller
{
    public function index(Request $request)
    {
        return $this->responseSuccess([
            'interests' => LeadInterest::getOptions(),
            'property_types' => PropertyType::getOptions(),
            'contact_methods' => LeadContactMethod::getOptions(),
            'contact_times' => LeadContactTime::getOptions(),
        ]);
    }

    public function submit(InquiryRequest $request)
    {
        DB::transaction(function () use ($request) {
            $lead = Lead::create($request->validated());

            event(new \App\Events\LeadSubmitted($lead));
        });

        return $this->responseSuccess([
            'message' => 'Inquiry submitted successfully',
        ]);
    }
}
