@extends('layouts.app')

@section('title', 'Services & keys')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Services &amp; keys</h1>
            <p class="page-sub">Each calling application gets a service ID and a gateway API key</p>
        </div>
    </div>

    @if (session('success'))
        @php $new = session('success'); @endphp
        <div class="alert alert-ok" role="status">
            <div class="alert-title">{{ is_array($new) ? $new['message'] : $new }}</div>
            @if (is_array($new))
                <p class="small">Copy the key now and store it in the calling app as <span class="mono">WHATSAPP_GATEWAY_API_KEY</span>.</p>
                <div class="secret-grid">
                    <span class="label">Service ID</span>
                    <span class="key"><span class="mono">{{ $new['service_id'] }}</span><button type="button" class="chip-btn" data-copy="{{ $new['service_id'] }}">Copy</button></span>
                    <span class="label">API key</span>
                    <span class="key"><span class="mono">{{ $new['api_key'] }}</span><button type="button" class="chip-btn" data-copy="{{ $new['api_key'] }}">Copy</button></span>
                </div>
            @endif
        </div>
    @endif

    <section class="card" id="new-service">
        <div class="card-head"><h2 class="card-title">Register a service</h2></div>
        <form action="{{ route('services.store') }}" method="POST">
            @csrf
            <div class="form-row" style="grid-template-columns: 1fr 1.3fr 1.3fr auto;">
                <div class="field">
                    <label class="label" for="service_name">Name</label>
                    <input type="text" id="service_name" name="service_name" class="input" value="{{ old('service_name') }}" placeholder="e.g. E-Khazna" required maxlength="255">
                </div>
                <div class="field">
                    <label class="label" for="allowed_templates">Allowed templates</label>
                    <input type="text" id="allowed_templates" name="allowed_templates" class="input" value="{{ old('allowed_templates') }}" placeholder="welcome_v1, otp_v2">
                </div>
                <div class="field">
                    <label class="label" for="description">Description</label>
                    <input type="text" id="description" name="description" class="input" value="{{ old('description') }}" placeholder="What uses this key" maxlength="1000">
                </div>
                <button type="submit" class="btn">Create service</button>
            </div>
            <p class="hint" style="margin-top:.5rem">Templates: comma-separated. Leave empty to allow every template.</p>
        </form>
    </section>

    <section class="card">
        <div class="card-head">
            <h2 class="card-title">
                @if ($search || $state)
                    {{ $services->count() }} of {{ $totalServices }} services
                @else
                    {{ $totalServices }} {{ Str::plural('service', $totalServices) }}
                @endif
            </h2>
            <form method="GET" action="{{ route('services.index') }}" class="actions" role="search">
                <label class="sr-only" for="s-q">Search services</label>
                <input class="input" style="width:220px" type="search" id="s-q" name="q" value="{{ $search }}" placeholder="Search name, ID, description">
                <label class="sr-only" for="s-state">Status</label>
                <select class="input" style="width:auto" id="s-state" name="state" onchange="this.form.submit()">
                    <option value="">All</option>
                    <option value="active" @selected($state === 'active')>Active</option>
                    <option value="inactive" @selected($state === 'inactive')>Inactive</option>
                </select>
                <button class="btn btn-ghost" type="submit">Search</button>
            </form>
        </div>

        @if ($services->isEmpty())
            <div class="empty">
                @if ($search || $state)
                    <strong>No services match</strong>
                    <a class="card-link" href="{{ route('services.index') }}">Clear the search</a>
                @else
                    <strong>No services yet</strong>
                    Register the first one above to get a service ID and API key.
                @endif
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Service ID</th>
                            <th>API key</th>
                            <th>Templates</th>
                            <th class="num">Sent · 7d</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($services as $service)
                            <tr>
                                <td>
                                    <strong>{{ $service->service_name }}</strong>
                                    @if ($service->description)
                                        <div class="muted small">{{ $service->description }}</div>
                                    @endif
                                    <div class="muted small">Created {{ $service->created_at->format('j M Y') }}</div>
                                </td>
                                <td>
                                    <span class="key">
                                        <span class="mono" style="color:#86efac">{{ $service->service_id }}</span>
                                        <button type="button" class="chip-btn" data-copy="{{ $service->service_id }}" aria-label="Copy service ID of {{ $service->service_name }}">Copy</button>
                                    </span>
                                </td>
                                <td>
                                    <span class="key">
                                        <span class="mono" style="color:#93c5fd" id="key-{{ $service->id }}" data-full="{{ $service->api_key }}" data-masked="{{ $service->maskedApiKey() }}" data-state="masked">{{ $service->maskedApiKey() }}</span>
                                        <button type="button" class="chip-btn" data-reveal="key-{{ $service->id }}">Show</button>
                                        <button type="button" class="chip-btn" data-copy="{{ $service->api_key }}" aria-label="Copy API key of {{ $service->service_name }}">Copy</button>
                                    </span>
                                </td>
                                <td style="max-width:220px">
                                    @forelse ($service->allowed_templates ?? [] as $tpl)
                                        <span class="tag">{{ $tpl }}</span>
                                    @empty
                                        <span class="muted small">All templates</span>
                                    @endforelse
                                </td>
                                <td class="num">
                                    <a class="row-link" href="{{ route('logs.index', ['service_name' => $service->service_name]) }}">{{ number_format($service->messages_7d) }}</a>
                                    @if ($service->failed_7d > 0)
                                        <div class="small" style="color:#fca5a5">{{ $service->failed_7d }} failed</div>
                                    @endif
                                    <div class="muted small nowrap">
                                        {{ $service->last_message_at ? 'last '.\Illuminate\Support\Carbon::parse($service->last_message_at)->diffForHumans() : 'never used' }}
                                    </div>
                                </td>
                                <td>
                                    @if ($service->is_active)
                                        <span class="badge badge-ok">Active</span>
                                    @else
                                        <span class="badge badge-off">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="actions">
                                        <button type="button" class="btn btn-ghost btn-sm" data-open-dialog="edit-{{ $service->id }}">Edit</button>
                                        <form action="{{ route('services.toggle-status', $service) }}" method="POST"
                                            @if ($service->is_active) data-confirm="Deactivate {{ $service->service_name }}? Its requests will be rejected until you activate it again." @endif>
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-ghost btn-sm">{{ $service->is_active ? 'Deactivate' : 'Activate' }}</button>
                                        </form>
                                        <form action="{{ route('services.regenerate-key', $service) }}" method="POST"
                                            data-confirm="Regenerate the API key for {{ $service->service_name }}? The current key stops working immediately.">
                                            @csrf
                                            <button type="submit" class="btn btn-warn btn-sm">New key</button>
                                        </form>
                                        <form action="{{ route('services.destroy', $service) }}" method="POST"
                                            data-confirm="Delete {{ $service->service_name }}? This cannot be undone. Its message history is kept.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    @foreach ($services as $service)
        @php $bag = $errors->getBag('edit_'.$service->id); @endphp
        <dialog id="edit-{{ $service->id }}" aria-labelledby="edit-title-{{ $service->id }}" @if ($bag->any()) data-open-on-load @endif>
            <form action="{{ route('services.update', $service) }}" method="POST">
                @csrf
                @method('PUT')
                <h2 class="card-title" id="edit-title-{{ $service->id }}" style="margin-bottom:1rem">Edit {{ $service->service_name }}</h2>
                @if ($bag->any())
                    <div class="alert alert-bad">{{ $bag->first() }}</div>
                @endif
                <div class="field">
                    <label class="label" for="e-name-{{ $service->id }}">Name</label>
                    <input class="input" id="e-name-{{ $service->id }}" name="service_name" required maxlength="255"
                        value="{{ $bag->any() ? old('service_name') : $service->service_name }}">
                </div>
                <div class="field">
                    <label class="label" for="e-desc-{{ $service->id }}">Description</label>
                    <textarea class="input" id="e-desc-{{ $service->id }}" name="description" maxlength="1000">{{ $bag->any() ? old('description') : $service->description }}</textarea>
                </div>
                <div class="field">
                    <label class="label" for="e-tpl-{{ $service->id }}">Allowed templates</label>
                    <textarea class="input" id="e-tpl-{{ $service->id }}" name="allowed_templates" placeholder="Empty = all templates">{{ $bag->any() ? old('allowed_templates') : implode(', ', $service->allowed_templates ?? []) }}</textarea>
                    <span class="hint">Comma-separated. Requests for any other template get a 403.</span>
                </div>
                <div class="dialog-actions">
                    <button type="button" class="btn btn-ghost" data-close-dialog>Cancel</button>
                    <button type="submit" class="btn">Save changes</button>
                </div>
            </form>
        </dialog>
    @endforeach

    <section class="card">
        <div class="card-head">
            <h2 class="card-title">Sending a message</h2>
            <button type="button" class="chip-btn" data-copy="" data-copy-from="curl-sample">Copy cURL</button>
        </div>
        <p class="muted small" style="margin-bottom:.8rem">
            Call <span class="mono">POST /api/v1/whatsapp/send-template</span> with the service ID in the body and the key in the
            <span class="mono">X-API-Key</span> header. Limit: 60 requests per minute.
        </p>
        <pre class="code" id="curl-sample">curl -X POST "{{ url('/api/v1/whatsapp/send-template') }}" \
  -H "X-API-Key: $WHATSAPP_GATEWAY_API_KEY" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "service_id": "{{ $exampleServiceId }}",
    "to": "91XXXXXXXXXX",
    "template": "your_template_name",
    "language": "en",
    "parameters": ["John"]
  }'</pre>
    </section>
@endsection

@push('scripts')
    <script>
        document.querySelector('dialog[data-open-on-load]')?.showModal();
    </script>
@endpush
