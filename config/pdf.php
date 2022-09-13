<?php
return
    [
        'mode'                  => 'utf-8',
        'format'                => 'A4',
        'author'                => '',
        'subject'               => '',
        'keywords'              => '',
        'creator'               => 'Laravel Pdf',
        'display_mode'          => 'fullpage',
        'tempDir'               => base_path('temp/'),
        'font_path' => base_path('public/assets/fonts/'),
        'font_data' => [
            'roboto' => [
                'R'  => 'Roboto-Regular.ttf',    // regular font
                'useOTL' => 0xFF,    // required for complicated langs like Persian, Arabic and Chinese
                'useKashida' => 75,  // required for complicated langs like Persian, Arabic and Chinese
            ],
            'hindsiliguri' => [
                'R'  => 'HindSiliguri-Regular.ttf',    // regular font
                'useOTL' => 0xFF,    // required for complicated langs like Persian, Arabic and Chinese
                'useKashida' => 75,  // required for complicated langs like Persian, Arabic and Chinese
            ],
            'arnamu' => [
                'R'  => 'arnamu.ttf',    // regular font
                'useOTL' => 0xFF,    // required for complicated langs like Persian, Arabic and Chinese
                'useKashida' => 75,  // required for complicated langs like Persian, Arabic and Chinese
            ],
            'varelaround' => [
                'R'  => 'VarelaRound-Regular.ttf',    // regular font
                'useOTL' => 0xFF,    // required for complicated langs like Persian, Arabic and Chinese
                'useKashida' => 75,  // required for complicated langs like Persian, Arabic and Chinese
            ],
            'hanuman' => [
                'R'  => 'Hanuman-Regular.ttf',    // regular font
                'useOTL' => 0xFF,    // required for complicated langs like Persian, Arabic and Chinese
                'useKashida' => 75,  // required for complicated langs like Persian, Arabic and Chinese
            ],
            'kanit' => [
                'R'  => 'Kanit-Regular.ttf',    // regular font
            ],
        ]
    ];