<?php

return [
    'af_css_with_widgets' => true, // If you move CSS to your app.css, you can set this to false
    'af_pruning' => 'never', // weekly | monthly | yearly | never -- this will delete both events and notifications themselves
    'af_channels' => ['email','cliq'],
    'af_tables' => [], // if you want to limit database tables for ruling
    'af_exclude_tables' => ['languages','sessions'] // you can exclude certain tables
];