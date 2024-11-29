<?php

namespace App\Http\Controllers;

use App\Enums\Lead\LeadContactMethod;
use App\Enums\Lead\LeadContactTime;
use App\Enums\Lead\LeadInterest;
use App\Enums\LeadStatus;
use App\Enums\PropertyAcquisitionType;
use App\Enums\PropertyType;
use App\Http\Requests\InquiryPropertyRequest;
use App\Http\Requests\InquiryRequest;
use App\Models\Lead;
use App\Models\Property;
use App\Models\Township;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
            $townshipId = null;
            if ($request->township) {
                Township::findBySlug($request->township)?->id;
            }

            $isOwner = match (LeadInterest::from($request->interest)) {
                LeadInterest::Renting => $request->is_owner,
                LeadInterest::Selling => true,
                LeadInterest::Buying => false
            };

            $lead = Lead::create([
                ...Arr::except($request->validated(), ['township', 'is_owner']),
                'is_owner' => $isOwner,
                'township_id' => $townshipId,
                'user_id' => $request->user()?->id,
                'status' => LeadStatus::New,
            ]);

            event(new \App\Events\LeadSubmitted($lead));
        });

        return $this->responseSuccess([], message: __('lead_trans.notification.submitted.title'));
    }

    public function submitProperty(InquiryPropertyRequest $request)
    {
        DB::transaction(function () use ($request) {
            $property = Property::where('code', $request->code)->first();

            $lead = Lead::create([
                'interest' => $property->acquisition_type == PropertyAcquisitionType::Rent ? LeadInterest::Renting : LeadInterest::Buying,
                'is_owner' => false,
                ...Arr::except($request->validated(), ['code', 'name']),
                'property_type' => $property->type,
                'first_name' => $request->name,
                'property_id' => $property?->id,
                'user_id' => $request->user()?->id,
                'status' => LeadStatus::New,
            ]);

            event(new \App\Events\LeadSubmitted($lead));
        });

        return $this->responseSuccess([], message: __('lead_trans.notification.submitted.title'));
    }
}
