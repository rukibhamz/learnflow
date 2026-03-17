<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Content Protection
    |--------------------------------------------------------------------------
    |
    | Controls the document protection features for course content.
    | These can be toggled per-environment (e.g. disable in local dev).
    |
    */

    'enabled' => env('CONTENT_PROTECTION_ENABLED', true),

    'disable_right_click' => true,

    'disable_text_selection' => true,

    'disable_copy_paste' => true,

    'disable_print' => true,

    'disable_dev_tools' => true,

    'blur_on_dev_tools' => true,

    'watermark' => [
        'enabled' => true,
        'opacity' => 0.06,
        'rotation' => -30,
    ],

    'video' => [
        'disable_download' => true,
        'disable_picture_in_picture' => true,
        'signed_url_expiry_minutes' => 30,
    ],

    'pdf' => [
        'inline_only' => true,
        'hide_toolbar' => true,
    ],

    'security_headers' => [
        'x_frame_options' => 'SAMEORIGIN',
        'x_content_type_options' => 'nosniff',
        'referrer_policy' => 'same-origin',
    ],
];
