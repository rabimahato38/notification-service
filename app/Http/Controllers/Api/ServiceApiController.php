<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceApiController extends Controller
{
    /**
     * Get a list of registered services.
     */
    public function index(): JsonResponse
    {
        $services = Service::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $services,
        ]);
    }

    /**
     * Create a new service configuration.
     * Automatically generates service_id and WHATSAPP_GATEWAY_API_KEY.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'allowed_templates' => ['nullable'],
        ]);

        $allowedTemplates = null;
        if (! empty($validated['allowed_templates'])) {
            if (is_array($validated['allowed_templates'])) {
                $allowedTemplates = array_values(array_filter(array_map('trim', $validated['allowed_templates'])));
            } elseif (is_string($validated['allowed_templates'])) {
                $allowedTemplates = array_values(array_filter(array_map('trim', explode(',', $validated['allowed_templates']))));
            }
        }

        $service = Service::create([
            'service_name' => $validated['service_name'],
            'description' => $validated['description'] ?? null,
            'allowed_templates' => $allowedTemplates,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Service configuration created successfully',
            'data' => [
                'id' => $service->id,
                'service_name' => $service->service_name,
                'service_id' => $service->service_id,
                'api_key' => $service->api_key,
                'allowed_templates' => $service->allowed_templates,
                'is_active' => $service->is_active,
                'description' => $service->description,
                'created_at' => $service->created_at,
            ],
        ], 201);
    }

    /**
     * Get details of a single service by service_id.
     */
    public function show(string $serviceId): JsonResponse
    {
        $service = Service::where('service_id', $serviceId)->first();

        if (! $service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $service,
        ]);
    }

    /**
     * Regenerate gateway API key for a service.
     */
    public function regenerateKey(string $serviceId): JsonResponse
    {
        $service = Service::where('service_id', $serviceId)->first();

        if (! $service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found',
            ], 404);
        }

        $service->update([
            'api_key' => Service::generateUniqueApiKey(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'API Key regenerated successfully',
            'data' => [
                'service_id' => $service->service_id,
                'service_name' => $service->service_name,
                'api_key' => $service->api_key,
            ],
        ]);
    }

    /**
     * Update allowed templates for a service by service_id.
     */
    public function updateTemplates(Request $request, string $serviceId): JsonResponse
    {
        $service = Service::where('service_id', $serviceId)->first();

        if (! $service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found',
            ], 404);
        }

        $validated = $request->validate([
            'allowed_templates' => ['nullable'],
        ]);

        $allowedTemplates = null;
        if (! empty($validated['allowed_templates'])) {
            if (is_array($validated['allowed_templates'])) {
                $allowedTemplates = array_values(array_filter(array_map('trim', $validated['allowed_templates'])));
            } elseif (is_string($validated['allowed_templates'])) {
                $allowedTemplates = array_values(array_filter(array_map('trim', explode(',', $validated['allowed_templates']))));
            }
        }

        $service->update([
            'allowed_templates' => $allowedTemplates,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Allowed templates updated successfully',
            'data' => [
                'service_id' => $service->service_id,
                'service_name' => $service->service_name,
                'allowed_templates' => $service->allowed_templates,
            ],
        ]);
    }
}
