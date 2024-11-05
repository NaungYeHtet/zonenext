<?php

namespace App\Mail\Lead;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class PropertyCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Property $property)
    {
        //
    }

    public function content(): Content
    {
        $owner = $this->property->owner;

        return new Content(
            markdown: 'mail.leads.property-created',
            with: [
                'userName' => $owner->name,
                'propertyTitle' => $this->property->title,
                'propertyLocation' => $this->property->address,
                'propertyPrice' => $this->property->price,
                'propertyType' => $this->property->type->getLabel(),
                'propertyDescription' => $this->property->description,
                'contactName' => $owner->admin->name,
                'contactNumber' => $owner->admin->phone,
                'contactEmail' => $owner->admin->email,
                'url' => config('app.frontent_url')."/listing/{$this->property->slug}",
            ],
        );
    }
}
