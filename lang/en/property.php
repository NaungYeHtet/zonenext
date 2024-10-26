<?php

return [
    'label' => 'Property',
    'status' => [
        'Rent' => 'Rent',
    ],
    'Age' => 'Age',
    'acquisition' => [
        'commission' => [
            'by_month' => ':percentage of monthly price',
            'by_month_50' => 'half of monthly price',
            'by_month_100' => 'one month price',
            'by_month_200' => 'tow months price',
            'by_month_300' => 'three months price',
            'by' => ':commission by :commission_by',
        ],
        'negotiable' => [
            'yes' => 'Yes',
            'no' => 'No',
        ],
    ],
    'actions' => [
        'post' => [
            'label' => 'Post',
        ],
        'complete' => [
            'label' => 'Complete',
        ],
        'sold' => [
            'label' => 'Sold',
        ],
        'rent' => [
            'label' => 'Rent',
        ],
    ],
    'settings' => [
        'form' => [
            'max_agent_assignment' => [
                'label' => 'Maxmium assignment of agent for a property',
            ],
            'max_rows_per_import' => [
                'label' => 'Maximum rows per property import',
            ],
        ],
    ],
];
