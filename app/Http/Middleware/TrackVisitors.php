<?php

// app/Http/Middleware/TrackVisitors.php
namespace App\Http\Middleware;

use Closure;
use App\Models\VisitorActivity;
use Carbon\Carbon;

class TrackVisitors
{
    public function handle($request, Closure $next)
    {
        if (!$request->is('track-activity/*')) {
            $today = Carbon::today();

            VisitorActivity::firstOrCreate(
                ['date' => $today],
                [
                    'visitors' => 0,
                    'contact_clicks' => 0,
                    'login_clicks' => 0,
                    'download_clicks' => 0
                ]
            )->increment('visitors');
        }

        return $next($request);
    }
}
