<?php

return [
    'action' => [
        'schedule' => [
            'label' => 'Schedule',
        ],
    ],
    'notification' => [
        'assigned' => [
            'title' => 'New Lead: :lead_name Assigned to You',
            'body' => 'You have a new lead assigned to you: :lead_name. Reach out to them at :contact and review the details in your dashboard or by clicking below button.',
        ],
        'property_created' => [
            'title' => 'Property created successfully',
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
