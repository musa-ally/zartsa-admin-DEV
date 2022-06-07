<?php

/*
    |--------------------------------------------------------------------------
    | Sending mail to Addresses
    |--------------------------------------------------------------------------
    |
    | This option controls the sending of emails to specified email addresses.
    | It is used when you want to alert other users of an important audit log.
    |
    */

return [
    'send_email_to' => '',
    'user_model' => App\Models\User::class,
    'foreign_key' => 'user_id',
    'owner_key' => 'id',
    'audit_logs_limit' => 50,
];
