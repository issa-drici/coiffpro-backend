<?php

return [
    'trial_days' => 30,
    
    'plans' => [
        'basic' => [
            'name' => 'Basic',
            'price' => 0,
            'stripe_price_id' => null,
            'id' => 'basic',
            'features' => [
                [
                    'name' => 'menu_categories',
                    'value' => 5,
                    'key' => 'menu_categories',
                    'label' => 'Catégories de menu'
                ],
                [
                    'name' => 'menu_items',
                    'value' => 15,
                    'key' => 'menu_items',
                    'label' => 'Articles au menu'
                ],
                [
                    'name' => 'qr_codes',
                    'value' => 1,
                    'key' => 'qr_codes',
                    'label' => 'QR codes personnalisables'
                ]
            ]
        ],
        'premium' => [
            'name' => 'Premium',
            'price' => 9.99,
            'stripe_price_id' => env('STRIPE_PREMIUM_PRICE_ID'),
            'id' => 'premium',
            'features' => [
                [
                    'name' => 'menu_categories',
                    'value' => 10,
                    'key' => 'menu_categories',
                    'label' => 'Catégories de menu'
                ],
                [
                    'name' => 'menu_items',
                    'value' => 50,
                    'key' => 'menu_items',
                    'label' => 'Articles au menu'
                ],
                [
                    'name' => 'qr_codes',
                    'value' => 5,
                    'key' => 'qr_codes',
                    'label' => 'QR codes personnalisables'
                ]
            ]
        ]
    ],
    
    'default_plan' => 'premium',
    'default_status' => 'trialing'
]; 