<?php

return [
    'label' => 'ပစ္စည်း',
    'status' => [
        'Rent' => 'ငှားပြီး',
    ],
    'Age' => 'သက်တမ်း',
    'acquisition' => [
        'commission' => [
            'by_month' => 'တစ်လစာ၏ :percentage',
            'by_month_50' => 'တစ်လစာ၏ တစ်ဝက်',
            'by_month_100' => 'တစ်လစာ',
            'by_month_200' => 'နှစ်လစာ',
            'by_month_300' => 'သုံးလစာ',
            'by' => ':commission_by မှ :commission',
        ],
        'negotiable' => [
            'yes' => 'ရ',
            'no' => 'မရ',
        ],
    ],
    'actions' => [
        'post' => [
            'label' => 'တင်မည်',
        ],
        'complete' => [
            'label' => 'ပြီးစီးသည်',
        ],
        'sold' => [
            'label' => 'ရောင်းပြီး',
        ],
        'rent' => [
            'label' => 'ငှားပြီး',
        ],
    ],
    'settings' => [
        'form' => [
            'max_agent_assignment' => [
                'label' => 'ပစ္စည်းတစ်ခုလျှင် တာဝန်ပေးနိုင်မည့် အေးဂျင့်အရေအတွက်',
            ],
            'max_rows_per_import' => [
                'label' => 'တစ်ကြိမ်လျှင် တင်သွင်းနိုင်မည့် ပစ္စည်းမှတ်တမ်းအရေအတွက်',
            ],
        ],
    ],
];
