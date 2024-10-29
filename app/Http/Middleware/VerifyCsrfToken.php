<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/register-user',
        'api/login-user',
        'api/login-google',
        'api/logout-user',
        'api/get-users',
        'api/update-user',
        'api/delete-user',
        'api/check-user-password',

        'google/callback',
        'google/redirect',
        'auth/google',
        'auth/google/callback',
        
        'api/forgot-password',
        'api/verify/pin',
        'api/reset-password',

        'api/get-categories',
        'api/add-category',
        'api/load-products-in-category',
        'api/load-products-in-category-id',
        'api/update-category',
        
        'api/get-brands',
        'api/load-products-in-brand',

        'api/get-products',
        'api/get-product-inf',
        'api/update-product',
        'api/delete-product',
        'api/add-product',

        'api/get-reviews',
        'api/add-new-review',

        'api/get-addresses',
        'api/add-new-address',
        'api/update-address',
        'api/delete-address',

        'api/get-cart',
        'api/add-to-cart',
        'api/remove-to-cart',
        'api/update-to-cart',
        'api/clear-cart',

        'api/get-favs',
        'api/add-to-favs',
        'api/check-faved',
        'api/remove-from-favs',
        
        'api/get-orders',
        'api/get-unique-years',
        'api/count-orders',
        'api/get-user-orders',
        'api/get-user-orders-details',
        'api/create-order',
        'api/update-delivered-date',
        'api/update-order-status',
        'api/update-payment-status',
        'api/evaluate-order',

        'api/create-order-details',

        'upload-img-product',
        'search-product',

        'vnpay',
        'momo',
        'next-order-id',

    ];
}
