<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'proverb'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'https://chengyuapi.2345.cn'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'ikey' => '-----BEGIN PRIVATE KEY-----
MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBAJ1OWR0WDJRlPZJw
2Y4v1VXf5iTQhvElXe8tKssUTu6jmQS3N0DXX5mLBONk3JojcH77Vf7+f1XCQzSn
+Evo4zsi3v0VWLPbmkopgTH0gxXpHtSGy9oCeQ7gw9kr3D19SnB32dub/taAFWyj
eAgHywWimMlTQ4zssxUCNesL1U5lAgMBAAECgYAu3Nxncd4lh1OkgMIPPejMQHuL
lCPfS0aErmwFO/OX3F0Frp9jTcZJM8UtCSs/MDefXLjt0luU7N6gnTm3Q5YN3F62
J5JhQEQv6juZL3HZBAurSXYjNxK9UC/vXAFUHj2XU9EQWNYdcsw+QE2Q4aiOBWpK
zNVmoGCo6MOBqTW3sQJBAM+e4+rVaYlPf7Pbn0RnOSVQ1y6D1SuE7x4sGscc0cKJ
BmpVPhrtP6xK4pst56hFjwft8ylLV1E6qMLmIpzz0QcCQQDB9g+JwHyVq1iCm52O
qNZXYpWWXIRuMFRhO35ABmG3sOzJsoFd8tMUEhCbg6WMqRRyl74aBczzHu3heeto
H4YzAkADKWJDIzjWNHW3YXLNcdz26oI8XRmT7hROG1HVEmVHVxrX57BobJB5qTJG
Nq6+a0DAWClJybHstL35KdQBG91lAkEAtdqEev00UgfS9rH8qXL3c/sEkXOw3rcF
hhyHpHPXTqjdecaZsIIpEZpWY9iscRNeDorjO/bQT+ph0pPC+V8FLQJBALHGGmbP
ALuu7WZwE77/eoEPSP0AToZ5lCBrly8ki75DVtV/FaRgbUNsoniysD7ACbYQVLHH
H7KpviA9/KftYqM=
-----END PRIVATE KEY-----',

    'pkey' => '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCdTlkdFgyUZT2ScNmOL9VV3+Yk
0IbxJV3vLSrLFE7uo5kEtzdA11+ZiwTjZNyaI3B++1X+/n9VwkM0p/hL6OM7It79
FViz25pKKYEx9IMV6R7UhsvaAnkO4MPZK9w9fUpwd9nbm/7WgBVso3gIB8sFopjJ
U0OM7LMVAjXrC9VOZQIDAQAB
-----END PUBLIC KEY-----',
];
