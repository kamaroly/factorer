<?php

return [
    'name' => 'Order',


    /**
     * List of available product and their prices
     */
    'products' => [
            [
                'id' => 1,
                'name' => 'Pack of 20 bottle of 300ml',
                'price' => 6.7
            ],
            [
                'id' => 2,
                'name' => 'Pack 12bottle of 500ml',
                'price' => 6.7
            ]
        ],

    /**
     * Available order statuses
     */
    'statuses' => [
        'processing'    => 'Processing',
        'paid'          => 'Paid',
        'completed'     => 'Completed',
        'returned'      => 'Returned',
    ],

    /**
     * Notification groups
     */
    'receptionists' => [
        'super@admin.com',
    ],
    'cashiers'              =>  [
        'executive@executive.com',
        'super@admin.com',
    ],
    'stock_managers'        =>  [
        'manager@manager.com',
    ],
    'security_controller'   =>  [
        'olivierbite@gmail.com',
    ],

    /**
     * Mapping status to the approvers
     */
    'order_approval_matrix' => [
        'processing'    => 'cashiers',
        'paid'          => 'stock_managers',
        'completed'     => 'security_controller',
    ],
];
