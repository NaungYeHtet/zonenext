<?php

return [
    'action' => [
        'schedule' => [
            'label' => 'Schedule',
        ],
    ],
    'notification' => [
        'submitted' => [
            'title' => "Thank you for submitting the inquiry! We'll get back to you shortly.",
        ],
        'assigned' => [
            'title' => 'New Lead: :lead_name Assigned to You',
            'body' => 'You have a new lead assigned to you: :lead_name. Reach out to them at :contact and review the details in your dashboard or by clicking below button.',
        ],
        'agent_assigned' => [
            'title' => 'Lead assigned to :agent successfully',
        ],
        'property_created' => [
            'title' => 'Property created successfully',
        ],
        'purchased' => [
            'title' => 'Property sold/rented successfully',
        ],
        'contacted' => [
            'title' => 'Lead marked as contacted successfully',
        ],
        'scheduled' => [
            'title' => 'Lead marked as scheduled successfully',
        ],
        'closed' => [
            'title' => 'Lead closed successfully',
        ],
    ],
];
