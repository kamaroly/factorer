<?php

return [
    'name' => 'Order',


    /**
     * List of available product and their prices
     */
    'products' => [
            [
                'id' => 1,
                'name' => 'Pack of 24 bottle of 300ml',
                'price' => 300
            ],
            [
                'id' => 2,
                'name' => 'Pack 20 bottle of 500ml',
                'price' => 500
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
    ]
];
