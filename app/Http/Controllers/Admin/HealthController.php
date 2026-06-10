<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class HealthController extends Controller
{
    public function index(): JsonResponse
    {
        // DB connectivity
        try {
            DB::select('SELECT 1');
            $dbOk = true;
        } catch (\Throwable) {
            $dbOk = false;
        }

        // Disk / storage usage
        $storagePath = storage_path('app/public');
        $totalBytes  = @disk_total_space($storagePath) ?: 0;
        $freeBytes   = @disk_free_space($storagePath)  ?: 0;
        $usedBytes   = $totalBytes - $freeBytes;

        // Queue
        $pendingJobs = 0;
        $failedJobs  = 0;
        try {
            $pendingJobs = DB::table('jobs')->count();
            $failedJobs  = DB::table('failed_jobs')->count();
        } catch (\Throwable) {}

        // Error count last 24 h
        $recentErrors = 0;
        try {
            $recentErrors = Activity::where('log_name', 'exception')
                ->where('created_at', '>=', now()->subDay())
                ->count();
        } catch (\Throwable) {}

        return response()->json([
            'db' => [
                'ok'     => $dbOk,
                'label'  => $dbOk ? 'Terhubung' : 'Error',
                'driver' => config('database.default'),
                'name'   => config('database.connections.' . config('database.default') . '.database'),
            ],
            'storage' => [
                'ok'       => $freeBytes > 104_857_600,
                'used_mb'  => $totalBytes > 0 ? round($usedBytes / 1_048_576, 1) : 0,
                'total_gb' => $totalBytes > 0 ? round($totalBytes / 1_073_741_824, 1) : 0,
                'free_gb'  => $totalBytes > 0 ? round($freeBytes / 1_073_741_824, 2) : 0,
                'used_pct' => $totalBytes > 0 ? round(($usedBytes / $totalBytes) * 100, 1) : 0,
            ],
            'queue' => [
                'ok'      => $pendingJobs < 100 && $failedJobs === 0,
                'pending' => $pendingJobs,
                'failed'  => $failedJobs,
            ],
            'app' => [
                'php_version'     => PHP_VERSION,
                'laravel_version' => app()->version(),
                'environment'     => app()->environment(),
                'debug'           => config('app.debug'),
                'recent_errors'   => $recentErrors,
            ],
        ]);
    }
}
