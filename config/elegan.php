<?php

return [
    /*
     * Automatic spaces
    */
    'space' => "\x20",

    /*
     * Default path to save and get patch note files
    */
    'patch_note_path' => public_path('elegan/versions'),

    /*
     * Used to recognize the parameter typed in the console
    */
    'parameter_identify' => ':',

    /*
     * Default file that will always be generated to store the http methods
    */
    'default_main_yaml' => '/actions.yaml',

    /*
     * Path to the folder where the actions.yaml files and their "methods".yaml will be located
    */
    'route_path' => public_path('elegan/routes'),

    /*
     * Key to access the documentation
    */
    'key' => env('ELEGAN_KEY', 'elegan'),

    /*
     * Default tries to access route 
    */
    'rate_limit' => 10,

    /*
     * How long the route will be blocked
    */
    'decay_minutes' => 10,

    /*
     * Message returned when redirected 
    */
    'redirect' => [
        'expire' => 'A chave expirou',
        'invalid_key' => 'Chave inválida',
    ],
];
