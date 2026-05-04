<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminAnalyticsService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request, AdminAnalyticsService $analytics): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'analytics' => $analytics->snapshot(),
        ]);
    }
}
