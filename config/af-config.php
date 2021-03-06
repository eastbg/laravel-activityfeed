<?php

/*
 * Config for ActivityFeed (vendor/east/LaravelActivityFeed/)
 * */

return [
    'af_css_with_widgets' => true, // If you move CSS to your app.css, you can set this to false
    'af_pruning' => 'never', // weekly | monthly | yearly | never -- this will delete both events and notifications themselves
    'af_channels' => ['email', 'cliq'],
    'af_tables' => [], // if you want to limit database tables for ruling
    'af_exclude_tables' => ['languages', 'sessions'], // you can exclude certain tables
    'af_model_path' => 'App\Models\Zoho\Modules\Models',
    'repeat_events_grace' => 1440,  // don't create similar events within the grace period
    //'af_email_mailer' => Email
];