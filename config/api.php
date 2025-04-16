<?php

return [
    'base_url' => env('BASE_URL_API'),
    'url_api' => [
        'sync_product_machine' => 'notification/mqtt',
        'notify_firebase' => 'notification/firebase'
    ]
];
