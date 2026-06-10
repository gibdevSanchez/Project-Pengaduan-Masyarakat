<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class LogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Activity::query()->latest();

        if ($request->type && $request->type !== 'all') {
            $query->where('log_name', $request->type);
        }

        $logs = $query->paginate(40);

        $logs->getCollection()->transform(function (Activity $log) {
            $causerName = null;
            if ($log->causer) {
                $causerName = $log->causer->nama_petugas
                    ?? $log->causer->nama
                    ?? 'Sistem';
            }

            $extra = collect($log->properties ?? [])->except('ip')->filter()->toArray();

            return [
                'id'          => $log->id,
                'type'        => $log->log_name,
                'description' => $log->description,
                'causer'      => $causerName,
                'ip'          => $log->properties['ip'] ?? null,
                'extra'       => $extra,
                'created_at'  => $log->created_at->format('d M Y, H:i:s'),
                'created_diff'=> $log->created_at->diffForHumans(),
            ];
        });

        return response()->json($logs);
    }
}
