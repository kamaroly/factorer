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
    ]
];
