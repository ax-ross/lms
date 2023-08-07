<?php

return [
    'url' => env('CENTRIFUGO_URL', 'http://centrifugo:8000'),
    'token_hmac_secret_key' => env('CENTRIFUGO_TOKEN_HMAC_SECRET_KEY',''),
    'api_key' => env('CENTRIFUGO_API_KEY',''),
    'connection_timeout' => env('CENTRIFUGO_CONNECTION_TIMEOUT', 10),
    'timeout' => env('CENTRIFUGO_TIMEOUT', 30),
    'use_assoc' => env('CENTRIFUGO_USE_ASSOC', false),
    'safety' => env('CENTRIFUGO_SAFETY', true),
    'cert' => env('CENTRIFUGO_CERT', ''),
    'ca_path' => env('CENTRIFUGO_CA_PATH', ''),
];