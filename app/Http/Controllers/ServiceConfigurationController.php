<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceConfigurationController extends Controller
{
    /**
     * Display a listing of services and the creation form.
     */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'in:active,inactive'],
        ]);
        $search = $filters['q'] ?? null;
        $state = $filters['state'] ?? null;

        $services = Service::query()
            ->withCount([
                'logs as messages_7d' => fn ($q) => $q->where('created_at', '>=', now()->subDays(7)),
                'logs as failed_7d' => fn ($q) => $q->where('created_at', '>=', now()->subDays(7))->where('status', '!=', 'success'),
            ])
            ->withMax('logs as last_message_at', 'created_at')
            ->when($search, function ($q, $v) {
                $like = '%'.addcslashes($v, '%_\\').'%';
                $q->where(fn ($w) => $w->where('service_name', 'like', $like)
                    ->orWhere('service_id', 'like', $like)
                    ->orWhere('description', 'like', $like));
            })
            ->when($state, fn ($q, $v) => $q->where('is_active', $v === 'active'))
            ->latest()
            ->get();

        return view('services.index', [
            'services' => $services,
            'totalServices' => Service::count(),
            'search' => $search,
            'state' => $state,
            'exampleServiceId' => Service::where('is_active', true)->latest()->value('service_id') ?? 'SRV-EXAMPLE1',
        ]);
    }

    /**
     * Update name, description and allowed templates of a service.
     */
    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validateWithBag('edit_'.$service->id, [
            'service_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'allowed_templates' => ['nullable', 'string', 'max:2000'],
        ]);

        $service->update([
            'service_name' => $validated['service_name'],
            'description' => $validated['description'] ?? null,
            'allowed_templates' => Service::parseTemplateList($validated['allowed_templates'] ?? null),
        ]);

        return redirect()->route('services.index')->with('status_message', "Service '{$service->service_name}' updated.");
    }

    /**
     * Store a newly created service configuration.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'allowed_templates' => ['nullable', 'string', 'max:2000'],
        ]);

        $service = Service::create([
            'service_name' => $validated['service_name'],
            'description' => $validated['description'] ?? null,
            'allowed_templates' => Service::parseTemplateList($validated['allowed_templates'] ?? null),
        ]);

        return redirect()->route('services.index')->with('success', [
            'message' => "Service configuration created successfully for '{$service->service_name}'!",
            'service_name' => $service->service_name,
            'service_id' => $service->service_id,
            'api_key' => $service->api_key,
        ]);
    }

    /**
     * Update configured allowed templates for a service.
     */
    public function updateTemplates(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'allowed_templates' => ['nullable', 'string', 'max:2000'],
        ]);

        $service->update([
            'allowed_templates' => Service::parseTemplateList($validated['allowed_templates'] ?? null),
        ]);

        return redirect()->route('services.index')->with('status_message', "Allowed templates updated for service '{$service->service_name}'.");
    }

    /**
     * Regenerate gateway API key for a service.
     */
    public function regenerateKey(Service $service): RedirectResponse
    {
        $service->update([
            'api_key' => Service::generateUniqueApiKey(),
        ]);

        return redirect()->route('services.index')->with('success', [
            'message' => "API Key regenerated for service '{$service->service_name}'!",
            'service_name' => $service->service_name,
            'service_id' => $service->service_id,
            'api_key' => $service->api_key,
        ]);
    }

    /**
     * Toggle active/inactive status of a service.
     */
    public function toggleStatus(Service $service): RedirectResponse
    {
        $service->update([
            'is_active' => ! $service->is_active,
        ]);

        $statusLabel = $service->is_active ? 'activated' : 'deactivated';

        return redirect()->route('services.index')->with('status_message', "Service '{$service->service_name}' has been {$statusLabel}.");
    }

    /**
     * Delete a service configuration.
     */
    public function destroy(Service $service): RedirectResponse
    {
        $serviceName = $service->service_name;
        $service->delete();

        return redirect()->route('services.index')->with('status_message', "Service configuration for '{$serviceName}' was deleted.");
    }
}
