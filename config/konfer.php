<?php

return [

    'htm' => 70000,
    'pendaftaran_sampai' => env('PENDAFTARAN_SAMPAI', '2025-06-30'),
    'kecamatan' => [
        '35.03.01' => 'Panggul',
        '35.03.02' => 'Munjungan',
        '35.03.03' => 'Pule',
        '35.03.04' => 'Dongko',
        '35.03.05' => 'Tugu',
        '35.03.06' => 'Karangan',
        '35.03.07' => 'Kampak',
        '35.03.08' => 'Watulimo',
        '35.03.09' => 'Bendungan',
        '35.03.10' => 'Gandusari',
        '35.03.11' => 'Trenggalek',
        '35.03.12' => 'Pogalan',
        '35.03.13' => 'Durenan',
        '35.03.14' => 'Suruh',
    ],

    'wa_api' => 'http://203.130.251.21:3000/send',

    'rekening' => [
        'name' => 'FARIDHOTUZ ZULFA KHU',
        'brand' => 'BRI',
        'no' => '655101031883533'
    ],
    
    'saedo_url' => env('SAEDO_URL', 'https://edo.local.com'),

];