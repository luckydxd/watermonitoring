<?php

namespace App\Http\Controllers;

use App\Models\VisitorActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function track($type)
    {
        $today = Carbon::today();

        $activity = VisitorActivity::firstOrCreate(
            ['date' => $today],
            [
                'visitors' => 0,
                'contact_clicks' => 0,
                'login_clicks' => 0,
                'download_clicks' => 0
            ]
        );

        // Hanya increment field yang sesuai, tanpa increment visitors
        switch ($type) {
            case 'contact':
                $activity->increment('contact_clicks');
                break;
            case 'login':
                $activity->increment('login_clicks');
                break;
            case 'download':
                $activity->increment('download_clicks');
                break;
        }

        return response()->json(['success' => true]);
    }
}
