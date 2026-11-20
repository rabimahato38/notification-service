@extends('layouts.app')

@section('title', 'Message log')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Message log</h1>
            <p class="page-sub">Every template request the gateway passed to the provider, newest first</p>
        </div>
        <a href="{{ route('logs.export', request()->query()) }}" class="btn btn-ghost">Download CSV</a>
    </div>

    <form class="card" method="GET" action="{{ route('logs.index') }}" role="search">
        <div class="form-row" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));">
            <div class="field">
                <label class="label" for="f-service">Service</label>
                <select class="input" id="f-service" name="service_name">
                    <option value="">All services</option>
                    @foreach ($serviceNames as $name)
                        <option value="{{ $name }}" @selected($filters['service_name'] === $name)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label class="label" for="f-template">Template</label>
                <select class="input" id="f-template" name="template">
                    <option value="">All templates</option>
                    @foreach ($templates as $tpl)
                        <option value="{{ $tpl }}" @selected($filters['template'] === $tpl)>{{ $tpl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label class="label" for="f-status">Result</label>
                <select class="input" id="f-status" name="status">
                    <option value="">Any</option>
                    <option value="success" @selected($filters['status'] === 'success')>Delivered</option>
                    <option value="failed" @selected($filters['status'] === 'failed')>Failed</option>
                </select>
            </div>
            <div class="field">
                <label class="label" for="f-from">From</label>
                <input class="input" type="date" id="f-from" name="from" value="{{ $filters['from'] }}">
            </div>
            <div class="field">
                <label class="label" for="f-to">To</label>
                <input class="input" type="date" id="f-to" name="to" value="{{ $filters['to'] }}">
            </div>
            <div class="field">
                <label class="label" for="f-q">Recipient contains</label>
                <input class="input" type="search" id="f-q" name="q" value="{{ $filters['q'] }}" placeholder="e.g. 7620" inputmode="numeric">
            </div>
            <div class="field" style="flex-direction: row; gap: .5rem;">
                <button class="btn" type="submit">Filter</button>
                @if (array_filter($filters))
                    <a class="btn btn-ghost" href="{{ route('logs.index') }}">Clear</a>
                @endif
            </div>
        </div>
        <p class="hint" style="margin-top:.6rem">Numbers are stored masked (first 4 and last 2 digits), so search on those.</p>
    </form>

    <section class="card">
        <div class="card-head">
            <h2 class="card-title">{{ number_format($logs->total()) }} {{ Str::plural('message', $logs->total()) }}</h2>
        </div>

        @if ($logs->isEmpty())
            <div class="empty">
                <strong>No messages match</strong>
                @if (array_filter($filters)) Try clearing a filter. @else Nothing has been sent through the gateway yet. @endif
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Service</th>
                            <th>Template</th>
                            <th>To</th>
                            <th>Lang</th>
                            <th>Result</th>
                            <th><span class="sr-only">Details</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td class="mono nowrap" title="{{ $log->created_at->diffForHumans() }}">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    {{ $log->service_name }}
                                    @if ($log->service_id)<div class="mono muted small">{{ $log->service_id }}</div>@endif
                                </td>
                                <td><span class="tag">{{ $log->template }}</span></td>
                                <td class="mono">{{ $log->to }}</td>
                                <td class="mono muted">{{ $log->language }}</td>
                                <td>@include('logs.partials.status', ['log' => $log])</td>
                                <td><a class="btn btn-ghost btn-sm" href="{{ route('logs.show', $log) }}">Details</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <nav class="pagination" aria-label="Pages">
                <span class="muted">Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ number_format($logs->total()) }}</span>
                <span class="actions">
                    @if ($logs->onFirstPage())
                        <span class="btn btn-ghost btn-sm" aria-disabled="true" style="opacity:.4">← Newer</span>
                    @else
                        <a class="btn btn-ghost btn-sm" href="{{ $logs->previousPageUrl() }}" rel="prev">← Newer</a>
                    @endif
                    @if ($logs->hasMorePages())
                        <a class="btn btn-ghost btn-sm" href="{{ $logs->nextPageUrl() }}" rel="next">Older →</a>
                    @else
                        <span class="btn btn-ghost btn-sm" aria-disabled="true" style="opacity:.4">Older →</span>
                    @endif
                </span>
            </nav>
        @endif
    </section>
@endsection
