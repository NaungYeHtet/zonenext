<?php

namespace App\Mail\Lead;

use App\Models\Lead;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class PropertyPurchased extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Property $property, public Lead $lead)
    {
        //
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $markdown = '';

        $with = [
            'userName' => $this->lead->name,
            'propertyTitle' => $this->property->title,
            'propertyLocation' => $this->property->address,
            'propertyPrice' => $this->property->price,
            'purchaseDate' => $this->property->purchased_at->format('Y-m-d'),
            'contactSupport' => config('app.support_email'),
        ];

        if ($this->lead->id == $this->property->owner_id) {
            $markdown = 'mail.leads.purchased-notice';
            $customer = $this->property->customer;

            $with['leadName'] = $customer->name;
            $with['leadEmail'] = $customer->email;
            $with['leadPhone'] = $customer->phone;
        } else {
            $markdown = 'mail.leads.purchased-confirmation';
        }

        return new Content(
            markdown: $markdown,
            with: $with,
        );
    }
}
