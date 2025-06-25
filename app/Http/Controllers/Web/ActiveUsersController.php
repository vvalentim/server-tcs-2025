<?php

namespace App\Http\Controllers\Web;

use App\Models\OnlineUserSession;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ActiveUsersController
{
    /**
     * Display a listing of the active users.
     */
    public function index(): View
    {
        OnlineUserSession::query()
            ->where('last_activity', '<=', now()->subHour())
            ->delete();

        $sessions = OnlineUserSession::query()
            ->select(
                'user_id',
                DB::raw('MAX(last_activity) as last_activity'),
                DB::raw('COUNT(user_id) AS session_count')
            )
            ->groupBy('user_id')
            ->orderBy('last_activity', 'desc')
            ->get();

        return view('active-users', [
            'sessions' => $sessions,
        ]);
    }
}
