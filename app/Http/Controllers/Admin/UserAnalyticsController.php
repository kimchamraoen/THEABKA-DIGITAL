<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UserAnalyticsController extends Controller
{
    public function summary(): JsonResponse
    {
        $totalLogins = DB::table('user_login_logs')->count();
        $mobileLogins = DB::table('user_login_logs')
            ->where(function ($query) {
                $query->whereRaw("LOWER(COALESCE(device_type, '')) LIKE ?", ['%mobile%'])
                    ->orWhereRaw("LOWER(COALESCE(device_type, '')) LIKE ?", ['%phone%'])
                    ->orWhereRaw("LOWER(COALESCE(device_type, '')) LIKE ?", ['%iphone%'])
                    ->orWhereRaw("LOWER(COALESCE(device_type, '')) LIKE ?", ['%android%']);
            })
            ->count();

        $data = [
            'total_users' => User::count(),
            'logins_today' => DB::table('user_login_logs')->whereDate('logged_in_at', today())->count(),
            'countries_reached' => DB::table('user_login_logs')->distinct('country')->count('country'),
            'mobile_percent' => round(
                $mobileLogins / max($totalLogins, 1) * 100
            ),
        ];

        return response()->json($data);
    }

    public function providerStats(): JsonResponse
    {
        $data = DB::table('user_login_logs')
            ->select('provider', DB::raw('count(*) as total'))
            ->groupBy('provider')
            ->orderByDesc('total')
            ->get();

        return response()->json($data);
    }

    public function locationStats(): JsonResponse
    {
        $data = DB::table('user_login_logs')
            ->select('country', 'city', DB::raw('count(*) as total'))
            ->groupBy('country', 'city')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return response()->json($data);
    }

    public function deviceStats(): JsonResponse
    {
        $data = DB::table('user_login_logs')
            ->select('device_type', DB::raw('count(*) as total'))
            ->groupBy('device_type')
            ->orderByDesc('total')
            ->get();

        return response()->json($data);
    }

    public function browserStats(): JsonResponse
    {
        $data = DB::table('user_login_logs')
            ->select('browser', DB::raw('count(*) as total'))
            ->groupBy('browser')
            ->orderByDesc('total')
            ->get();

        return response()->json($data);
    }

    public function osStats(): JsonResponse
    {
        $data = DB::table('user_login_logs')
            ->select('os', DB::raw('count(*) as total'))
            ->groupBy('os')
            ->orderByDesc('total')
            ->get();

        return response()->json($data);
    }

    public function registrationTimeline(): JsonResponse
    {
        $data = DB::table('users')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get();

        return response()->json($data);
    }

    public function loginTimeline(): JsonResponse
    {
        $data = DB::table('user_login_logs')
            ->selectRaw('DATE(logged_in_at) as date, COUNT(*) as total')
            ->where('logged_in_at', '>=', now()->subDays(30))
            ->groupByRaw('DATE(logged_in_at)')
            ->orderBy('date')
            ->get();

        return response()->json($data);
    }
}
