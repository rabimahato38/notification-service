<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MessageLogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->filters($request);

        return view('logs.index', [
            'logs' => $this->query($filters)->paginate(25)->withQueryString(),
            'filters' => $filters,
            'serviceNames' => WhatsAppLog::query()->distinct()->orderBy('service_name')->pluck('service_name'),
            'templates' => WhatsAppLog::query()->distinct()->orderBy('template')->pluck('template'),
        ]);
    }

    public function show(WhatsAppLog $log): View
    {
        return view('logs.show', ['log' => $log]);
    }

    /**
     * Download the filtered log as CSV (same filters as the list).
     */
    public function export(Request $request): StreamedResponse
    {
        $query = $this->query($this->filters($request));

        return response()->streamDownload(function () use ($query): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'created_at', 'service_id', 'service_name', 'to', 'template', 'language', 'status', 'status_code', 'parameters']);

            foreach ($query->reorder()->lazyByIdDesc(500, 'id') as $log) {
                fputcsv($out, array_map([$this, 'csvSafe'], [
                    $log->id,
                    $log->created_at?->toDateTimeString(),
                    $log->service_id,
                    $log->service_name,
                    $log->to,
                    $log->template,
                    $log->language,
                    $log->status,
                    $log->status_code,
                    json_encode($log->parameters ?? []),
                ]));
            }

            fclose($out);
        }, 'message-log-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * @return array<string, string|null>
     */
    private function filters(Request $request): array
    {
        $validated = $request->validate([
            'service_name' => ['nullable', 'string', 'max:255'],
            'template' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:success,failed'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'q' => ['nullable', 'string', 'max:50'],
        ]);

        return array_merge(array_fill_keys(['service_name', 'template', 'status', 'from', 'to', 'q'], null), $validated);
    }

    /**
     * @param  array<string, string|null>  $f
     * @return Builder<WhatsAppLog>
     */
    private function query(array $f): Builder
    {
        return WhatsAppLog::query()
            ->when($f['service_name'], fn ($q, $v) => $q->where('service_name', $v))
            ->when($f['template'], fn ($q, $v) => $q->where('template', $v))
            ->when($f['status'] === 'success', fn ($q) => $q->where('status', 'success'))
            ->when($f['status'] === 'failed', fn ($q) => $q->where('status', '!=', 'success'))
            ->when($f['from'], fn ($q, $v) => $q->where('created_at', '>=', Carbon::parse($v)->startOfDay()))
            ->when($f['to'], fn ($q, $v) => $q->where('created_at', '<=', Carbon::parse($v)->endOfDay()))
            ->when($f['q'], fn ($q, $v) => $q->where('to', 'like', '%'.addcslashes($v, '%_\\').'%'))
            ->orderByDesc('id');
    }

    /**
     * Stop spreadsheet apps from treating a cell as a formula.
     */
    private function csvSafe(mixed $value): mixed
    {
        if (is_string($value) && $value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'".$value;
        }

        return $value;
    }
}
