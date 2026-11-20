@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-sub">WhatsApp template traffic through the gateway</p>
        </div>
        <a href="{{ route('services.index') }}#new-service" class="btn">+ New service</a>
    </div>

    <section class="stats" aria-label="Key figures">
        <div class="stat">
            <div class="stat-label">Messages today</div>
            <div class="stat-value">{{ number_format($stats['today']) }}</div>
            <div class="stat-note">{{ number_format($stats['week_total']) }} in the last 7 days</div>
        </div>
        <div class="stat">
            <div class="stat-label">Success rate · 7 days</div>
            <div class="stat-value">{{ $stats['success_rate'] === null ? '—' : $stats['success_rate'].'%' }}</div>
            @if ($stats['success_rate'] === null)
                <div class="stat-note">No messages yet</div>
            @elseif ($stats['success_rate'] < 95)
                <div class="stat-note bad">Below 95%: check failures</div>
            @else
                <div class="stat-note good">Healthy</div>
            @endif
        </div>
        <div class="stat">
            <div class="stat-label">Failed · 24 hours</div>
            <div class="stat-value">{{ number_format($stats['failed_24h']) }}</div>
            <div class="stat-note">
                @if ($stats['failed_24h'] > 0)
                    <a class="card-link" href="{{ route('logs.index', ['status' => 'failed']) }}">View failures</a>
                @else
                    Nothing to look at
                @endif
            </div>
        </div>
        <div class="stat">
            <div class="stat-label">Active services</div>
            <div class="stat-value">{{ $stats['services_active'] }}<span class="muted" style="font-size:1rem"> / {{ $stats['services_total'] }}</span></div>
            <div class="stat-note"><a class="card-link" href="{{ route('services.index') }}">Manage keys</a></div>
        </div>
    </section>

    @php
        $max = max(1, collect($daily)->max(fn ($d) => $d['ok'] + $d['failed']));
        $w = 700; $h = 190; $pad = 22; $slot = $w / count($daily); $barW = $slot * 0.62;
        $scale = ($h - $pad - 8) / $max;
    @endphp

    <div class="grid grid-3-1">
        <section class="card">
            <div class="card-head">
                <h2 class="card-title">Messages per day · last 14 days</h2>
                <div class="legend"><span class="l-ok">Delivered</span><span class="l-bad">Failed</span></div>
            </div>
            <svg class="chart" viewBox="0 0 {{ $w }} {{ $h }}" role="img" aria-labelledby="chart-desc">
                <desc id="chart-desc">Daily delivered and failed messages for the last 14 days; peak {{ $max }} per day.</desc>
                <line x1="0" x2="{{ $w }}" y1="{{ $h - $pad }}" y2="{{ $h - $pad }}" stroke="#26334a" />
                <text x="0" y="10" fill="#8b98ad" font-size="10">{{ $max }}</text>
                @foreach ($daily as $i => $d)
                    @php
                        $x = $i * $slot + ($slot - $barW) / 2;
                        $okH = $d['ok'] * $scale; $badH = $d['failed'] * $scale;
                        $base = $h - $pad;
                    @endphp
                    <g>
                        <title>{{ $d['date']->format('D j M') }}: {{ $d['ok'] }} delivered, {{ $d['failed'] }} failed</title>
                        <rect x="{{ $x }}" y="{{ $base - $okH }}" width="{{ $barW }}" height="{{ $okH }}" fill="#22c55e" rx="2" />
                        <rect x="{{ $x }}" y="{{ $base - $okH - $badH }}" width="{{ $barW }}" height="{{ $badH }}" fill="#ef4444" rx="2" />
                        <rect x="{{ $i * $slot }}" y="0" width="{{ $slot }}" height="{{ $h }}" fill="transparent" />
                    </g>
                    @if ($i % 2 === 0 || $loop->last)
                        <text x="{{ $i * $slot + $slot / 2 }}" y="{{ $h - 6 }}" fill="#8b98ad" font-size="10" text-anchor="middle">{{ $d['date']->format('j M') }}</text>
                    @endif
                @endforeach
            </svg>
        </section>

        <section class="card">
            <div class="card-head"><h2 class="card-title">Top templates · 7 days</h2></div>
            @include('dashboard.partials.bar-list', ['rows' => $topTemplates, 'filter' => 'template'])
        </section>
    </div>

    <div class="grid grid-3-1">
        <section class="card">
            <div class="card-head">
                <h2 class="card-title">Recent messages</h2>
                <a class="card-link" href="{{ route('logs.index') }}">Open message log →</a>
            </div>
            @if ($recent->isEmpty())
                <div class="empty">
                    <strong>No messages yet</strong>
                    Send one through <span class="mono">POST /api/v1/whatsapp/send-template</span> and it shows up here.
                </div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr><th>When</th><th>Service</th><th>Template</th><th>To</th><th>Result</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($recent as $log)
                                <tr>
                                    <td class="nowrap"><a class="row-link" href="{{ route('logs.show', $log) }}" title="{{ $log->created_at }}">{{ $log->created_at->diffForHumans() }}</a></td>
                                    <td>{{ $log->service_name }}</td>
                                    <td><span class="tag">{{ $log->template }}</span></td>
                                    <td class="mono">{{ $log->to }}</td>
                                    <td>@include('logs.partials.status', ['log' => $log])</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="card">
            <div class="card-head"><h2 class="card-title">Busiest services · 7 days</h2></div>
            @include('dashboard.partials.bar-list', ['rows' => $topServices, 'filter' => 'service'])
        </section>
    </div>
@endsection
