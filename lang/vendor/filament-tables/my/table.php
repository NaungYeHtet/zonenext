<?php

return [

    'column_toggle' => [

        'heading' => 'ကော်လံများ',

    ],

    'columns' => [

        'text' => [

            'actions' => [
                'collapse_list' => 'Show :count less',
                'expand_list' => 'Show :count more',
            ],

            'more_list_items' => 'and :count more',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Select/deselect all items for bulk actions.',
        ],

        'bulk_select_record' => [
            'label' => 'Select/deselect item :key for bulk actions.',
        ],

        'bulk_select_group' => [
            'label' => 'Select/deselect group :title for bulk actions.',
        ],

        'search' => [
            'label' => 'ရှာမည်',
            'placeholder' => 'ရှာမည်',
            'indicator' => 'ရှာထားထည်',
        ],

    ],

    'summary' => [

        'heading' => 'အကျဥ်းချုပ်',

        'subheadings' => [
            'all' => ':label အားလုံး',
            'group' => ':group အကျဥ်းချုပ်',
            'page' => 'လက်ရှိစာမျက်နှာ',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'ပျမ်းမျှ',
            ],

            'count' => [
                'label' => 'အရေအတွက်',
            ],

            'sum' => [
                'label' => 'ပေါင်းလဒ်',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'မှတ်တမ်းများပြန်စီခြင်း ပြီးပါပြီ',
        ],

        'enable_reordering' => [
            'label' => 'မှတ်တမ်းများပြန်စီမည်',
        ],

        'filter' => [
            'label' => 'စစ်ထုတ်ခြင်း',
        ],

        'group' => [
            'label' => 'အုပ်စု',
        ],

        'open_bulk_actions' => [
            'label' => 'အစုလိုက် လုပ်ဆောင်ခြင်းများ',
        ],

        'toggle_columns' => [
            'label' => 'Toggle columns',
        ],

    ],

    'empty' => [

        'heading' => ':model မရှိပါ',

        'description' => 'Create a :model to get started.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'စစ်ထုတ်မည်',
            ],

            'remove' => [
                'label' => 'စစ်ထားသည်ကို ဖြုတ်မည်',
            ],

            'remove_all' => [
                'label' => 'စစ်ထားသည်များအားလုံး ဖြုတ်မည်',
                'tooltip' => 'စစ်ထားသည်များအားလုံး ဖြုတ်မည်',
            ],

            'reset' => [
                'label' => 'ပြန်စမည်',
            ],

        ],

        'heading' => 'စစ်ထုတ်ခြင်း',

        'indicator' => 'စစ်ထုတ်ထားသည်',

        'multi_select' => [
            'placeholder' => 'အားလုံး',
        ],

        'select' => [
            'placeholder' => 'အားလုံး',
        ],

        'trashed' => [

            'label' => 'Deleted records',

            'only_trashed' => 'Only deleted records',

            'with_trashed' => 'With deleted records',

            'without_trashed' => 'Without deleted records',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Group by',
                'placeholder' => 'Group by',
            ],

            'direction' => [

                'label' => 'Group direction',

                'options' => [
                    'asc' => 'အသေးဆုံး',
                    'desc' => 'အကြီးဆုံး',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Drag and drop the records into order.',

    'selection_indicator' => [

        'selected_count' => 'မှတ်တမ်း:countခု ရွေးထားသည်',

        'actions' => [

            'select_all' => [
                'label' => ':countခုလုံး ရွေးမည်',
            ],

            'deselect_all' => [
                'label' => 'ရွေးထားသည်များ ဖြုတ်မည်',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'ဖြင့်စီမည်',
            ],

            'direction' => [

                'label' => 'Sort direction',

                'options' => [
                    'asc' => 'အသေးဆုံး',
                    'desc' => 'အကြီးဆုံး',
                ],

            ],

        ],

    ],

];
