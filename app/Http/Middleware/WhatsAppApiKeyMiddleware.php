<?php

namespace App\Http\Middleware;

use App\Models\Service;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WhatsAppApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $providedKey = $request->header('X-API-Key') ?? $request->header('X-Gateway-Api-Key');
        $hasKey = is_string($providedKey) && trim($providedKey) !== '';

        $serviceId = $request->input('service_id') ?? $request->header('X-Service-ID');
        $hasServiceId = is_string($serviceId) && trim($serviceId) !== '';

        // 1. Both service_id and API key header are missing
        if (! $hasServiceId && ! $hasKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Both service_id parameter and X-API-Key header are missing.',
            ], 401);
        }

        // 2. Gateway API key header is missing
        if (! $hasKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Gateway API Key is missing in X-API-Key header.',
            ], 401);
        }

        // 3. service_id parameter is missing
        if (! $hasServiceId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. service_id is required in request parameters.',
            ], 401);
        }

        /** @var Service|null $service */
        $service = Service::where('service_id', $serviceId)->first();

        $globalKey = (string) config('services.whatsapp_gateway.api_key');
        $isGlobalKeyMatch = $globalKey !== '' && hash_equals($globalKey, $providedKey);

        // 4. Service ID not found in database
        if (! $service) {
            if ($isGlobalKeyMatch) {
                return $next($request);
            }

            return response()->json([
                'success' => false,
                'message' => "Unauthorized. Service ID '{$serviceId}' not found. Invalid service ID.",
            ], 401);
        }

        // 5. Service ID is inactive
        if (! $service->is_active) {
            if ($isGlobalKeyMatch) {
                return $next($request);
            }

            return response()->json([
                'success' => false,
                'message' => "Unauthorized. Service ID '{$serviceId}' is inactive.",
            ], 401);
        }

        // 6. API Key does not match the service's assigned API key
        if (! hash_equals($service->api_key, $providedKey)) {
            if ($isGlobalKeyMatch) {
                $request->attributes->set('validated_service', $service);

                return $next($request);
            }

            return response()->json([
                'success' => false,
                'message' => "Unauthorized. Invalid API Key provided for Service ID '{$serviceId}'.",
            ], 401);
        }

        $request->attributes->set('validated_service', $service);

        $template = $request->input('template');
        if (is_string($template) && trim($template) !== '') {
            if (! $service->isTemplateAllowed($template)) {
                $allowedList = ! empty($service->allowed_templates) ? implode(', ', $service->allowed_templates) : 'None';

                return response()->json([
                    'success' => false,
                    'message' => "Unauthorized. Template '{$template}' is not configured or allowed for Service ID '{$serviceId}'. Allowed templates: [{$allowedList}].",
                ], 403);
            }
        }

        return $next($request);
    }
}
