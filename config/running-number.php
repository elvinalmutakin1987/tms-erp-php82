<?php

use CleaniqueCoders\RunningNumber\Enums\Organization;
use CleaniqueCoders\RunningNumber\Enums\ResetPeriod;
use CleaniqueCoders\RunningNumber\Generator;
use CleaniqueCoders\RunningNumber\Models\RunningNumber;
use CleaniqueCoders\RunningNumber\Presenter;

return [
    /*
    |--------------------------------------------------------------------------
    | Running Number Types
    |--------------------------------------------------------------------------
    |
    | These value is the types of the running number you would like to create.
    | The values can be any list of strings. As long listed here, the types
    | are supported by your application to generate the running number.
    | It is recommended to use Laravel Spatie Enum for these values.
    |
    | Following are the sample values based on organization perspective.
    | You may extend to documents, assets, etc.
    |
    */

    'types' => [
        // Organization::organization()->value,
        // Organization::division()->value,
        // Organization::section()->value,
        // Organization::unit()->value,
        // Organization::profile()->value,
        'p2h', //Untuk P2H
        'inv', //Untuk invoice  
        'po', //Untuk purchase order
        'rep', //Untuk daily report
        'pop', //Untuk pembayaran po
        'invp', //Untuk pembayaran invocie
        'comm', //Buat penomoran commisioning
        'serv', //Untuk nomor service
        'main', //Untuk penomoran maintenance
        'insp', //Untuk penomoran maintenance
    ],

    'model' => RunningNumber::class,

    /*
    |--------------------------------------------------------------------------
    | Running Number Generator
    |--------------------------------------------------------------------------
    |
    | Extend this claass to ovewrite the generate() method, to implement your
    | own generator.
    |
    */

    'generator' => Generator::class,

    /*
    |--------------------------------------------------------------------------
    | Running Number Concatenator
    |--------------------------------------------------------------------------
    |
    | This class how you expect the output of the running number after it
    | was created. The desire format can be C0005, C-0005.
    |
    */

    'presenter' => Presenter::class,

    /*
    |--------------------------------------------------------------------------
    | Running Number Padding Number
    |--------------------------------------------------------------------------
    |
    | This will reflect the generated code, such as 0005 if value set to 3.
    |
    */

    'padding' => 4,

    /*
    |--------------------------------------------------------------------------
    | Reset Period Configuration
    |--------------------------------------------------------------------------
    |
    | Configure when running numbers should automatically reset. You can set
    | a global default reset period, or configure specific periods per type.
    |
    | Available periods: 'never', 'daily', 'monthly', 'yearly'
    | - never: Running numbers never reset (default)
    | - daily: Reset at midnight every day
    | - monthly: Reset on the 1st of each month
    | - yearly: Reset on January 1st each year
    |
    */

    'reset_period' => [
        // 'default' => ResetPeriod::NEVER->value,

        // Per-type reset periods (optional)
        // Uncomment and configure specific types as needed
        // 'types' => [
        //     'invoice' => ResetPeriod::YEARLY->value,
        //     'receipt' => ResetPeriod::MONTHLY->value,
        //     'ticket' => ResetPeriod::DAILY->value,
        // ],
        'types' => [
            'p2h' => ResetPeriod::YEARLY->value,
            'inv' => ResetPeriod::YEARLY->value,
            'po' => ResetPeriod::YEARLY->value,
            'rep' => ResetPeriod::YEARLY->value,
            'pop' => ResetPeriod::YEARLY->value,
            'invp' => ResetPeriod::YEARLY->value,
            'comm' => ResetPeriod::YEARLY->value,
            'serv' => ResetPeriod::YEARLY->value,
            'main' => ResetPeriod::YEARLY->value,
            'insp' => ResetPeriod::YEARLY->value,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the REST API endpoints for generating and checking running
    | numbers via HTTP. Set 'enabled' to true to activate the API routes.
    |
    */

    'api' => [
        // Enable or disable API routes
        'enabled' => env('RUNNING_NUMBER_API_ENABLED', false),

        // API route prefix (default: api/running-numbers)
        'prefix' => env('RUNNING_NUMBER_API_PREFIX', 'api/running-numbers'),

        // API middleware
        'middleware' => ['api'],

        // Optional authentication middleware
        // Uncomment to require authentication for API endpoints
        // 'middleware' => ['api', 'auth:sanctum'],
    ],
];
