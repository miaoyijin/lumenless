<?php

return [
    'achivement' => [
        'baseUrl' => env('DEPEND_ACHIVE_BASE_URL', ''),
        'domain'  => env('DEPEND_ACHIVE_DOMAIN', ''),
        'signKey' => env('DEPEND_ACHIVE_SIGN_KEY', ''),
        //项目标识
        'project' => env('DEPEND_ACHIVE_PROJECT', '')
    ],
    'cheat' => [
        'baseUrl' => env('DEPEND_CHEAT_BASE_URL', ''),
        'domain'  => env('DEPEND_CHEAT_DOMAIN', '')
    ],
    'xq' => [
        'baseUrl' => env('DEPEND_XQ_BASE_URL', ''),
        'domain'  => env('DEPEND_XQ_DOMAIN', ''),
        'signKey' => env('DEPEND_XQ_SIGN_KEY', ''),
        'project' => env('DEPEND_XQ_PROJECT', '')
    ],
    'ref' => [
        'baseUrl' => env('DEPEND_REF_BASE_URL', ''),
        'domain'  => env('DEPEND_REF_DOMAIN', ''),
        'headers' => env('DEPEND_REF_HEADERS', '')
    ]
];
