<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendWhatsAppTemplateRequest;
use App\Services\WhatsApp\PinbotWhatsAppService;
use Illuminate\Http\JsonResponse;

class WhatsAppController extends Controller
{
    /**
     * Send a WhatsApp template message to recipient.
     */
    public function sendTemplate(
        SendWhatsAppTemplateRequest $request,
        PinbotWhatsAppService $whatsappService
    ): JsonResponse {
        $to = (string) $request->validated('to');
        $template = (string) $request->validated('template');
        $language = $request->getLanguage();
        $parameters = $request->getParameters();
        $serviceId = $request->getServiceId();
        $serviceName = $request->getServiceName();

        $result = $whatsappService->sendTemplate(
            to: $to,
            template: $template,
            language: $language,
            parameters: $parameters,
            serviceName: $serviceName,
            serviceId: $serviceId
        );

        return response()->json($result['body'], $result['status_code']);
    }
}
