@extends('layouts.app')

@section('title', 'Message #'.$log->id)

@section('content')
    <div class="page-head">
        <div>
            <p class="small"><a class="card-link" href="{{ url()->previous() !== url()->current() ? url()->previous() : route('logs.index') }}">← Back to message log</a></p>
            <h1 class="page-title">Message #{{ $log->id }}</h1>
            <p class="page-sub">{{ $log->created_at->format('l j F Y, H:i:s') }} ({{ $log->created_at->diffForHumans() }})</p>
        </div>
        @include('logs.partials.status', ['log' => $log])
    </div>

    <div class="grid grid-2">
        <section class="card">
            <div class="card-head"><h2 class="card-title">Request</h2></div>
            <div class="secret-grid" style="grid-template-columns: 130px 1fr">
                <span class="label">Service</span><span>{{ $log->service_name }}</span>
                <span class="label">Service ID</span><span class="mono">{{ $log->service_id ?? '—' }}</span>
                <span class="label">Recipient</span><span class="mono">{{ $log->to }}</span>
                <span class="label">Template</span><span><span class="tag">{{ $log->template }}</span></span>
                <span class="label">Language</span><span class="mono">{{ $log->language }}</span>
                <span class="label">HTTP status</span><span class="mono">{{ $log->status_code }}</span>
            </div>
            <h3 class="label" style="margin:1.1rem 0 .4rem">Parameters</h3>
            @if (empty($log->parameters))
                <p class="muted small">None</p>
            @else
                <ol style="margin-left:1.2rem" class="small">
                    @foreach ($log->parameters as $param)
                        <li><span class="mono">{{ is_scalar($param) ? $param : json_encode($param) }}</span></li>
                    @endforeach
                </ol>
            @endif
        </section>

        <section class="card">
            <div class="card-head">
                <h2 class="card-title">Provider response</h2>
                <button type="button" class="chip-btn" data-copy-from="response-json" data-copy="">Copy JSON</button>
            </div>
            <pre class="code" id="response-json">{{ json_encode($log->response ?? new stdClass, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
        </section>
    </div>
@endsection
