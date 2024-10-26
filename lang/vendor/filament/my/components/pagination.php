<?php

return [

    'label' => 'Pagination navigation',

    'overview' => '{1} Showing 1 result|[2,*] Showing :first to :last of :total results',

    'fields' => [

        'records_per_page' => [

            'label' => 'Per page',

            'options' => [
                'all' => 'အားလုံး',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => ':page စာမျက်နှာက်ုသွားမည်',
        ],

        'next' => [
            'label' => 'နောက်သို့',
        ],

        'previous' => [
            'label' => 'ရှေ့သို့',
        ],

    ],

];
