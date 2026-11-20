<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\WhatsAppLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const CHART_DAYS = 14;

    public function __invoke(): View
    {
        $now = Carbon::now();
        $weekAgo = $now->copy()->subDays(7);

        $week = WhatsAppLog::where('created_at', '>=', $weekAgo)
            ->selectRaw("COUNT(*) as total, SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as ok")
            ->first();
        $weekTotal = (int) $week->total;
        $weekOk = (int) $week->ok;

        $stats = [
            'services_total' => Service::count(),
            'services_active' => Service::where('is_active', true)->count(),
            'today' => WhatsAppLog::where('created_at', '>=', $now->copy()->startOfDay())->count(),
            'week_total' => $weekTotal,
            'success_rate' => $weekTotal > 0 ? round($weekOk / $weekTotal * 100, 1) : null,
            'failed_24h' => WhatsAppLog::where('status', '!=', 'success')
                ->where('created_at', '>=', $now->copy()->subDay())
                ->count(),
        ];

        return view('dashboard.index', [
            'stats' => $stats,
            'daily' => $this->dailySeries($now),
            'topTemplates' => $this->topBy('template', $weekAgo),
            'topServices' => $this->topBy('service_name', $weekAgo),
            'recent' => WhatsAppLog::latest()->limit(8)->get(),
        ]);
    }

    /**
     * One row per day for the chart, including days with no traffic.
     *
     * @return array<int, array{date: Carbon, ok: int, failed: int}>
     */
    private function dailySeries(Carbon $now): array
    {
        $start = $now->copy()->subDays(self::CHART_DAYS - 1)->startOfDay();

        $rows = WhatsAppLog::where('created_at', '>=', $start)
            ->selectRaw("DATE(created_at) as day, SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as ok, SUM(CASE WHEN status = 'success' THEN 0 ELSE 1 END) as failed")
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->keyBy(fn ($row) => Carbon::parse($row->day)->toDateString());

        $series = [];
        for ($i = 0; $i < self::CHART_DAYS; $i++) {
            $date = $start->copy()->addDays($i);
            $row = $rows->get($date->toDateString());
            $series[] = [
                'date' => $date,
                'ok' => (int) ($row->ok ?? 0),
                'failed' => (int) ($row->failed ?? 0),
            ];
        }

        return $series;
    }

    /**
     * @return Collection<int, object{label: string, total: int, failed: int}>
     */
    private function topBy(string $column, Carbon $since)
    {
        return WhatsAppLog::where('created_at', '>=', $since)
            ->selectRaw("{$column} as label, COUNT(*) as total, SUM(CASE WHEN status = 'success' THEN 0 ELSE 1 END) as failed")
            ->groupBy($column)
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }
}
