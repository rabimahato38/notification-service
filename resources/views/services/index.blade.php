<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Configurations — E-Khazna WhatsApp Gateway</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0f172a;
            --bg-card: #1e293b;
            --bg-input: #0f172a;
            --border-color: #334155;
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --emerald: #10b981;
            --emerald-bg: rgba(16, 185, 129, 0.15);
            --amber: #f59e0b;
            --rose: #ef4444;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            line-height: 1.6;
            padding: 2rem 1rem;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(18, 140, 126, 0.4);
        }

        .brand-icon svg {
            width: 24px;
            height: 24px;
            fill: #ffffff;
        }

        .brand-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .brand-subtitle {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .badge-total {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 0.4rem 0.9rem;
            border-radius: 20px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .badge-total span {
            color: var(--primary);
            font-weight: 700;
        }

        .card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.75rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }

        .card-title {
            font-size: 1.15rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.1) 100%);
            border: 1px solid rgba(16, 185, 129, 0.4);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 2rem;
        }

        .alert-title {
            font-weight: 700;
            color: #34d399;
            font-size: 1.05rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
            margin-top: 0.75rem;
        }

        .alert-field {
            background: var(--bg-dark);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .alert-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }

        .alert-value-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }

        .alert-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.9rem;
            color: #6ee7b7;
            word-break: break-all;
        }

        .copy-btn {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .copy-btn:hover {
            background: var(--primary);
            border-color: var(--primary);
        }

        .alert-status {
            background: rgba(59, 130, 246, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.4);
            color: #93c5fd;
            padding: 0.9rem 1.25rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr auto;
            gap: 1rem;
            align-items: flex-end;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-muted);
        }

        .form-input {
            background-color: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: var(--text-main);
            font-size: 0.95rem;
            font-family: inherit;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .btn {
            background-color: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.4rem;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-muted);
        }

        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--text-main);
        }

        .btn-sm {
            padding: 0.35rem 0.75rem;
            font-size: 0.8rem;
            border-radius: 6px;
        }

        .btn-warning {
            background-color: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: var(--amber);
        }

        .btn-warning:hover {
            background-color: rgba(245, 158, 11, 0.3);
        }

        .btn-danger {
            background-color: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: var(--rose);
        }

        .btn-danger:hover {
            background-color: rgba(239, 68, 68, 0.3);
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.9rem;
        }

        th {
            background-color: rgba(15, 23, 42, 0.6);
            padding: 0.9rem 1rem;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.85rem;
        }

        .badge-status {
            padding: 0.25rem 0.6rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-active {
            background-color: var(--emerald-bg);
            color: var(--emerald);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-inactive {
            background-color: rgba(239, 68, 68, 0.15);
            color: var(--rose);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .actions-cell {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .key-mask {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .code-box {
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.25rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.85rem;
            color: #38bdf8;
            overflow-x: auto;
            position: relative;
        }

        .code-box-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
            color: var(--text-muted);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.8rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-muted);
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="brand">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.84 9.84 0 0 0 12.04 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="brand-title">Services Configuration</h1>
                    <p class="brand-subtitle">WhatsApp API Gateway Service &amp; Key Registry</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="badge-total">
                    Registered Services: <span>{{ $services->count() }}</span>
                </div>
                @auth
                    <div style="display: flex; align-items: center; gap: 0.75rem; background: var(--bg-card); border: 1px solid var(--border-color); padding: 0.4rem 0.9rem; border-radius: 20px; font-size: 0.85rem;">
                        <span style="color: var(--text-muted);">Admin: <strong style="color: var(--text-main);">{{ Auth::user()->name ?? Auth::user()->email }}</strong></span>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="copy-btn" style="background: rgba(239, 68, 68, 0.2); border-color: rgba(239, 68, 68, 0.4); color: #fca5a5;">
                                Logout
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </header>

        <!-- Flash Success Alert for Created/Regenerated Service -->
        @if (session('success'))
            @php $successData = session('success'); @endphp
            <div class="alert-success">
                <div class="alert-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ is_array($successData) ? $successData['message'] : $successData }}
                </div>
                @if (is_array($successData))
                    <div class="alert-grid">
                        <div class="alert-field">
                            <div class="alert-label">Service Name</div>
                            <div class="alert-value" style="color: #ffffff;">{{ $successData['service_name'] }}</div>
                        </div>
                        <div class="alert-field">
                            <div class="alert-label">Auto-Generated Service ID</div>
                            <div class="alert-value-wrap">
                                <span class="alert-value" id="new-service-id">{{ $successData['service_id'] }}</span>
                                <button class="copy-btn"
                                    onclick="copyToClipboard('{{ $successData['service_id'] }}', this)">Copy</button>
                            </div>
                        </div>
                        <div class="alert-field" style="grid-column: 1 / -1;">
                            <div class="alert-label">Gateway API Key (WHATSAPP_GATEWAY_API_KEY)</div>
                            <div class="alert-value-wrap">
                                <span class="alert-value" id="new-api-key">{{ $successData['api_key'] }}</span>
                                <button class="copy-btn"
                                    onclick="copyToClipboard('{{ $successData['api_key'] }}', this)">Copy Key</button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        @if (session('status_message'))
            <div class="alert-status">
                {{ session('status_message') }}
            </div>
        @endif

        <!-- Card: Create New Service -->
        <div class="card">
            <h2 class="card-title">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Register New Service Configuration
            </h2>
            <form action="{{ route('services.store') }}" method="POST">
                @csrf
                <div class="form-grid" style="grid-template-columns: 1fr 1fr 1.5fr auto;">
                    <div class="form-group">
                        <label class="form-label" for="service_name">Service Name *</label>
                        <input type="text" id="service_name" name="service_name" class="form-input"
                            placeholder="e.g. E-Khazna" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="allowed_templates">Configured Templates *</label>
                        <input type="text" id="allowed_templates" name="allowed_templates" class="form-input"
                            placeholder="e.g. e_khazana_welcome, e_khazana_2 (or leave empty for all)" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="description">Description (Optional)</label>
                        <input type="text" id="description" name="description" class="form-input"
                            placeholder="Short service description">
                    </div>
                    <div>
                        <button type="submit" class="btn">
                            Create Service
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Card: Services Table -->
        <div class="card">
            <h2 class="card-title">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                    </path>
                </svg>
                Configured Services Registry
            </h2>

            @if ($services->isEmpty())
                <div class="empty-state">
                    <p>No service configurations found. Enter a service name above to create your first configuration!
                    </p>
                </div>
            @else
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Service Name</th>
                                <th>Service ID</th>
                                <th>Gateway API Key</th>
                                <th>Allowed Templates</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($services as $service)
                                <tr>
                                    <td>
                                        <strong>{{ $service->service_name }}</strong>
                                        @if ($service->description)
                                            <div style="font-size: 0.78rem; color: var(--text-muted);">
                                                {{ $service->description }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="key-mask">
                                            <span class="mono"
                                                style="color: #6ee7b7;">{{ $service->service_id }}</span>
                                            <button class="copy-btn"
                                                onclick="copyToClipboard('{{ $service->service_id }}', this)">Copy</button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="key-mask">
                                            <span class="mono" id="key-{{ $service->id }}"
                                                style="color: #38bdf8;">
                                                {{ Str::limit($service->api_key, 16, '...') }}
                                            </span>
                                            <button class="copy-btn"
                                                onclick="copyToClipboard('{{ $service->api_key }}', this)">Copy
                                                Key</button>
                                        </div>
                                    </td>
                                    <td>
                                        @if (!empty($service->allowed_templates))
                                            <div style="display:flex; flex-wrap:wrap; gap:0.25rem;">
                                                @foreach ($service->allowed_templates as $tpl)
                                                    <span class="mono"
                                                        style="background: rgba(59,130,246,0.15); border:1px solid rgba(59,130,246,0.3); color:#93c5fd; padding:0.15rem 0.4rem; border-radius:4px; font-size:0.75rem;">{{ $tpl }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span style="color: var(--text-muted); font-size: 0.8rem; italic;">All
                                                Templates Allowed</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($service->is_active)
                                            <span class="badge-status badge-active">Active</span>
                                        @else
                                            <span class="badge-status badge-inactive">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="mono" style="font-size: 0.8rem; color: var(--text-muted);">
                                        {{ $service->created_at->format('Y-m-d H:i') }}
                                    </td>
                                    <td>
                                        <div class="actions-cell">
                                            <form action="{{ route('services.toggle-status', $service) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-secondary btn-sm">
                                                    {{ $service->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                            <form action="{{ route('services.regenerate-key', $service) }}"
                                                method="POST" style="display:inline;"
                                                onsubmit="return confirm('Regenerate API Key for {{ $service->service_name }}?')">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm">
                                                    Regenerate Key
                                                </button>
                                            </form>
                                            <form action="{{ route('services.destroy', $service) }}" method="POST"
                                                style="display:inline;"
                                                onsubmit="return confirm('Delete configuration for {{ $service->service_name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Card: Integration Usage Guide -->
        <div class="card">
            <h2 class="card-title">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                How to Send API Request with Service ID &amp; Gateway API Key
            </h2>
            <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1rem;">
                When calling the WhatsApp Gateway API (<code>POST /api/v1/whatsapp/send-template</code>), pass your
                auto-generated <code>service_id</code> in the JSON body and your <code>X-API-Key</code> in the HTTP
                header:
            </p>
            <div class="code-box">
                <div class="code-box-header">
                    <span>cURL API Example</span>
                    <button class="copy-btn"
                        onclick="copyToClipboard(document.getElementById('curl-sample').innerText, this)">Copy
                        cURL</button>
                </div>
                <pre id="curl-sample">curl -X POST "{{ url('/api/v1/whatsapp/send-template') }}" \
  -H "X-API-Key: YOUR_WHATSAPP_GATEWAY_API_KEY" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "service_id": "{{ $services->first()?->service_id ?? 'SRV-EXAMPLE1' }}",
    "to": "7620476512",
    "template": "e_khazana_2",
    "language": "en",
    "parameters": ["John"]
  }'</pre>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text, btnElement) {
            navigator.clipboard.writeText(text).then(function() {
                const originalText = btnElement.innerText;
                btnElement.innerText = 'Copied!';
                btnElement.style.background = '#10b981';
                btnElement.style.borderColor = '#10b981';
                setTimeout(function() {
                    btnElement.innerText = originalText;
                    btnElement.style.background = '';
                    btnElement.style.borderColor = '';
                }, 1800);
            }, function(err) {
                alert('Failed to copy: ' + err);
            });
        }
    </script>
</body>

</html>
