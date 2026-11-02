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
    public function index(): View
    {
        $services = Service::latest()->get();

        return view('services.index', compact('services'));
    }

    /**
     * Store a newly created service configuration.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'allowed_templates' => ['nullable', 'string'],
        ]);

        $allowedTemplates = null;
        if (! empty($validated['allowed_templates'])) {
            $allowedTemplates = array_values(array_filter(array_map('trim', explode(',', $validated['allowed_templates']))));
        }

        $service = Service::create([
            'service_name' => $validated['service_name'],
            'description' => $validated['description'] ?? null,
            'allowed_templates' => $allowedTemplates,
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
            'allowed_templates' => ['nullable', 'string'],
        ]);

        $allowedTemplates = null;
        if (! empty($validated['allowed_templates'])) {
            $allowedTemplates = array_values(array_filter(array_map('trim', explode(',', $validated['allowed_templates']))));
        }

        $service->update([
            'allowed_templates' => $allowedTemplates,
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
