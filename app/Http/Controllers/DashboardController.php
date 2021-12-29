<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index(?string $slug = null) {
        $slug = $slug ?? 'dashboard';
        return inertia(Str::ucfirst($slug), [
            'menu' => [
                [
                    'title' => __('service/dashboard.menu.dashboard'),
                    'route' => 'dashboard',
                    'icon'  => 'fas fa-tachometer-alt mr-2'
                ],
                [
                    'title' => __('service/dashboard.menu.pages'),
                    'route' => [
                        'name'  => 'dashboard',
                        'props' => [
                            'slug' => 'pages'
                        ]
                    ],
                    'icon'  => 'fas fa-tachometer-alt mr-2'
                ]
            ]
        ]);
    }
}
