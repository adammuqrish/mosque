<?php

return [
    'roles' => [
        'admin' => [
            'name' => 'Administrator',
            'permissions' => [
                'view-dashboard',
                'create-events',
                'delete-events',
                'create-donations',
                'view-all-donations',
                'create-withdrawal-requests',
                'view-reports',
                'view-transparency',
                'update-own-profile',
                'join-events',
            ],
        ],
        'treasurer' => [
            'name' => 'Treasurer',
            'permissions' => [
                'view-dashboard',
                'approve-withdrawals',
                'reject-withdrawals',
                'view-all-donations',
                'view-reports',
                'view-transparency',
                'update-own-profile',
                'join-events',
            ],
        ],
        'member' => [
            'name' => 'Member',
            'permissions' => [
                'view-dashboard',
                'view-transparency',
                'update-own-profile',
                'join-events',
            ],
        ],
    ],

    'special_codes' => array_filter([
        env('ADMIN_CODE') => 'admin',
        env('TREASURER_CODE') => 'treasurer',
    ], function ($key) {
        return is_string($key) && $key !== '';
    }, ARRAY_FILTER_USE_KEY),
];
