<?php

return [

    /*
     * The public API (routes/api.php) is read-only and consumed by the
     * VANTA MOTO frontend. Admin panel pages are served from the same
     * origin, so a permissive origin list is acceptable here.
     */

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
