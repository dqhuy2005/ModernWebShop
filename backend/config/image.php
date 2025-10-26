<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. You may choose one of them according to your PHP
    | configuration. By default PHP's "GD Library" implementation is used.
    |
    | Supported: "gd", "imagick"
    |
    */
    'driver' => 'gd',

    /*
    |--------------------------------------------------------------------------
    | Image Sizes Configuration
    |--------------------------------------------------------------------------
    |
    | Define different image sizes for various purposes
    |
    */
    'sizes' => [
        'product' => [
            'thumbnail' => [
                'width' => 150,
                'height' => 150,
                'aspect_ratio' => true,
            ],
            'medium' => [
                'width' => 400,
                'height' => 400,
                'aspect_ratio' => true,
            ],
            'large' => [
                'width' => 800,
                'height' => 800,
                'aspect_ratio' => true,
            ],
        ],
        'category' => [
            'thumbnail' => [
                'width' => 150,
                'height' => 150,
                'aspect_ratio' => true,
            ],
            'medium' => [
                'width' => 400,
                'height' => 400,
                'aspect_ratio' => true,
            ],
            'large' => [
                'width' => 600,
                'height' => 600,
                'aspect_ratio' => true,
            ],
        ],
        'avatar' => [
            'thumbnail' => [
                'width' => 50,
                'height' => 50,
                'aspect_ratio' => false,
            ],
            'medium' => [
                'width' => 150,
                'height' => 150,
                'aspect_ratio' => false,
            ],
            'large' => [
                'width' => 300,
                'height' => 300,
                'aspect_ratio' => false,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Quality
    |--------------------------------------------------------------------------
    |
    | Define the quality of the resized images (1-100)
    | Higher value = better quality but larger file size
    |
    */
    'quality' => 85,

    /*
    |--------------------------------------------------------------------------
    | Image Upload Paths
    |--------------------------------------------------------------------------
    |
    | Define storage paths for different image types
    |
    */
    'paths' => [
        'products' => 'products',
        'avatars' => 'avatars',
        'categories' => 'categories',
        'temp' => 'temp',
    ],

    /*
    |--------------------------------------------------------------------------
    | Max File Size
    |--------------------------------------------------------------------------
    |
    | Maximum file size in kilobytes
    |
    */
    'max_size' => 2048, // 2MB

    /*
    |--------------------------------------------------------------------------
    | Allowed Extensions
    |--------------------------------------------------------------------------
    |
    | Allowed image file extensions
    |
    */
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
];
