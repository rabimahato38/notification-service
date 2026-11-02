<?php

use App\Http\Controllers\Api\ServiceApiController;
use App\Http\Controllers\Api\WhatsAppController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Gateway API routes for WhatsApp message delivery & Service Management.
|
*/

// Health check endpoint (No authentication required)
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'service' => 'E-Khazna WhatsApp API Gateway',
        'status' => 'healthy',
    ]);
});

// Service Configuration endpoints (API-based service creation)
Route::prefix('v1/services')->group(function () {
    Route::get('/', [ServiceApiController::class, 'index']);
    Route::post('/', [ServiceApiController::class, 'store']);
    Route::get('/{service_id}', [ServiceApiController::class, 'show']);
    Route::post('/{service_id}/regenerate-key', [ServiceApiController::class, 'regenerateKey']);
    Route::put('/{service_id}/templates', [ServiceApiController::class, 'updateTemplates']);
});

// Authenticated Gateway endpoints
Route::middleware(['whatsapp.api.key', 'throttle:60,1'])->prefix('v1/whatsapp')->group(function () {
    Route::post('/send-template', [WhatsAppController::class, 'sendTemplate']);
});
