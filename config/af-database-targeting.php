<?php

/*
 * Refer to documentation for the logic of targeting / routing. Change from one table always
 * needs to lead to users table to be targeted.
 * */

// todo: support any depth of relationships
return [
    'tables' => [
        'Accounts' => [
            [
                'id' => 'account_contacts',
                'title' => 'Account Contacts',
                'description' => '',
                'relations' => ['Contacts', 'User']
            ],
        ],
        'Deals' => [
            [
                'id' => 'related_contacts',
                'title' => 'Related Contacts',
                'description' => '',
                'relations' => ['VendorList', 'contact', 'users']
            ],
        ],
        'Candidates' => [
            [
                'id' => 'candidate_contacts',
                'title' => 'Candidate Contacts',
                'description' => '',
                'relations' => ['account', 'contact', 'users']
            ],
        ],
        'Candidates_X_Deals' => [
            [
                'id' => 'candidate_account_contact',
                'title' => 'Account Contacts from Deal',
                'description' => '',
                'relations' => ['account', 'contact', 'users']
            ],
        ]
    ],
];