<?php

return [

    'label' => ':label တင်သွင်းမည်',

    'modal' => [

        'heading' => ':label တင်သွင်းမည်',

        'form' => [

            'file' => [
                'label' => 'ဖိုင်',
                'placeholder' => 'CSV ဖိုင်တစ်ခုဖြင့် တင်သွင်းမည်',
            ],

            'columns' => [
                'label' => 'ကော်လံများ',
                'placeholder' => 'ကော်လံတစ်ခု ရွေးချယ်မည်',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'ဥပမာ CSVဖိုင် တစ်ခု ဒေါင်းလုပ်ရယူမည်',
            ],

            'import' => [
                'label' => 'တင်သွင်းမည်',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'တင်သွင်းပြီးပါပြီ',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Download information about the failed row|Download information about the failed rows',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Uploaded CSV file is too large',
            'body' => 'You may not import more than 1 row at once.|You may not import more than :count rows at once.',
        ],

        'started' => [
            'title' => 'Import started',
            'body' => 'Your import has begun and 1 row will be processed in the background.|Your import has begun and :count rows will be processed in the background.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'error',
        'system_error' => 'System error, please contact support.',
    ],

];
