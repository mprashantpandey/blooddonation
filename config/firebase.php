<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging (HTTP v1)
    |--------------------------------------------------------------------------
    |
    | Use a Firebase service account JSON (same as Google Cloud). Set either
    | FIREBASE_SERVICE_ACCOUNT_PATH to a readable file path, or inline JSON /
    | base64 for container-friendly secrets. FCM_PROJECT_ID is optional if the
    | JSON includes project_id.
    |
    */

    'service_account_path' => env('FIREBASE_SERVICE_ACCOUNT_PATH'),

    'service_account_json' => env('FIREBASE_SERVICE_ACCOUNT_JSON'),

    'service_account_base64' => env('FIREBASE_SERVICE_ACCOUNT_BASE64'),

    'project_id' => env('FCM_PROJECT_ID'),

];
