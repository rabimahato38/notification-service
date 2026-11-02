<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsAppLog;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PinbotWhatsAppService
{
    /**
     * Send a WhatsApp template message via Pinbot API and log to DB.
     *
     * @param  array<int, string>  $parameters
     * @return array<string, mixed>
     */
    public function sendTemplate(
        string $to,
        string $template,
        string $language = 'en',
        array $parameters = [],
        string $serviceName = 'general',
        ?string $serviceId = null
    ): array {
        $baseUrl = rtrim((string) config('services.pinbot.base_url', 'https://partnersv1.pinbot.ai'), '/');
        $phoneNumberId = (string) config('services.pinbot.phone_number_id');
        $apiKey = (string) config('services.pinbot.api_key');
        $timeout = (int) config('services.pinbot.timeout', 30);
        $connectTimeout = (int) config('services.pinbot.connect_timeout', 10);

        $url = "{$baseUrl}/v3/{$phoneNumberId}/messages";
        $maskedPhone = $this->maskPhoneNumber($to);
        $paramCount = count($parameters);

        Log::info('Initiating WhatsApp template request to Pinbot', [
            'service_id' => $serviceId,
            'service_name' => $serviceName,
            'to' => $maskedPhone,
            'template' => $template,
            'language' => $language,
            'parameter_count' => $paramCount,
            'target_url' => $url,
        ]);

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $template,
                'language' => [
                    'code' => $language,
                ],
                'components' => $this->buildComponents($parameters),
            ],
        ];

        try {
            $response = Http::timeout($timeout)
                ->connectTimeout($connectTimeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'apikey' => $apiKey,
                ])
                ->post($url, $payload);

            $statusCode = $response->status();
            $responseData = $response->json() ?? [];

            Log::info('Pinbot HTTP response received', [
                'service_id' => $serviceId,
                'service_name' => $serviceName,
                'to' => $maskedPhone,
                'template' => $template,
                'status_code' => $statusCode,
                'response' => $responseData,
            ]);

            if ($response->successful()) {
                $data = [
                    'service_name' => $serviceName,
                    'to' => $to,
                    'template' => $template,
                    'language' => $language,
                    'parameters' => $parameters,
                    'provider_response' => $responseData,
                ];

                if ($serviceId !== null) {
                    $data = array_merge(['service_id' => $serviceId], $data);
                }

                $result = [
                    'status_code' => 200,
                    'body' => [
                        'success' => true,
                        'message' => 'WhatsApp template sent successfully',
                        'data' => $data,
                    ],
                ];

                $this->saveDatabaseLog($serviceName, $maskedPhone, $template, $language, $parameters, $responseData, 200, 'success', $serviceId);

                return $result;
            }

            $result = $this->formatPinbotErrorResponse($statusCode, $responseData);
            $this->saveDatabaseLog($serviceName, $maskedPhone, $template, $language, $parameters, $responseData, $result['status_code'], 'failed', $serviceId);

            return $result;
        } catch (ConnectionException $e) {
            Log::error('Pinbot connection timeout or error', [
                'service_id' => $serviceId,
                'service_name' => $serviceName,
                'to' => $maskedPhone,
                'template' => $template,
                'error' => $e->getMessage(),
            ]);

            $result = [
                'status_code' => 504,
                'body' => [
                    'success' => false,
                    'message' => 'Gateway timeout while communicating with WhatsApp provider',
                    'error' => [
                        'provider' => 'pinbot',
                        'type' => 'ConnectionException',
                        'details' => 'Connection to provider timed out or failed',
                    ],
                ],
            ];

            $this->saveDatabaseLog($serviceName, $maskedPhone, $template, $language, $parameters, $result['body'], 504, 'failed', $serviceId);

            return $result;
        } catch (Throwable $e) {
            Log::error('Unexpected error while calling Pinbot API', [
                'service_id' => $serviceId,
                'service_name' => $serviceName,
                'to' => $maskedPhone,
                'template' => $template,
                'error' => $e->getMessage(),
            ]);

            $result = [
                'status_code' => 500,
                'body' => [
                    'success' => false,
                    'message' => 'Internal server error while processing WhatsApp request',
                    'error' => [
                        'provider' => 'pinbot',
                        'type' => get_class($e),
                        'details' => $e->getMessage(),
                    ],
                ],
            ];

            $this->saveDatabaseLog($serviceName, $maskedPhone, $template, $language, $parameters, $result['body'], 500, 'failed', $serviceId);

            return $result;
        }
    }

    /**
     * Save message request & response log to database.
     *
     * @param  array<int, string>  $parameters
     * @param  array<string, mixed>  $response
     */
    protected function saveDatabaseLog(
        string $serviceName,
        string $to,
        string $template,
        string $language,
        array $parameters,
        array $response,
        int $statusCode,
        string $status,
        ?string $serviceId = null
    ): void {
        try {
            WhatsAppLog::create([
                'service_id' => $serviceId,
                'service_name' => $serviceName,
                'to' => $to,
                'template' => $template,
                'language' => $language,
                'parameters' => $parameters,
                'response' => $response,
                'status_code' => $statusCode,
                'status' => $status,
            ]);
        } catch (Throwable $dbEx) {
            Log::error('Failed to write WhatsApp log to database', [
                'service_id' => $serviceId,
                'service_name' => $serviceName,
                'error' => $dbEx->getMessage(),
            ]);
        }
    }

    /**
     * Build Pinbot template components structure based on dynamic parameters.
     *
     * @param  array<int, string>  $parameters
     * @return array<int, mixed>
     */
    protected function buildComponents(array $parameters): array
    {
        if (empty($parameters)) {
            return [];
        }

        $formattedParams = array_map(static function ($param) {
            return [
                'type' => 'text',
                'text' => (string) $param,
            ];
        }, array_values($parameters));

        return [
            [
                'type' => 'body',
                'parameters' => $formattedParams,
            ],
        ];
    }

    /**
     * Format Pinbot HTTP error payload into normalized Gateway response.
     *
     * @param  array<string, mixed>  $responseData
     * @return array<string, mixed>
     */
    protected function formatPinbotErrorResponse(int $statusCode, array $responseData): array
    {
        $error = $responseData['error'] ?? [];

        $code = $error['code'] ?? null;
        $type = $error['type'] ?? 'ProviderException';
        $details = $error['error_data']['details'] ?? $error['message'] ?? 'Provider error occurred';

        return [
            'status_code' => 502,
            'body' => [
                'success' => false,
                'message' => 'WhatsApp provider rejected the template request',
                'error' => array_filter([
                    'provider' => 'pinbot',
                    'code' => $code,
                    'type' => $type,
                    'details' => $details,
                ], static fn ($value) => $value !== null),
            ],
        ];
    }

    /**
     * Mask sensitive mobile numbers for logging.
     * e.g., "7620476512" -> "7620****12"
     */
    protected function maskPhoneNumber(string $number): string
    {
        $length = strlen($number);
        if ($length <= 4) {
            return str_repeat('*', $length);
        }
        if ($length <= 6) {
            return substr($number, 0, 2).str_repeat('*', $length - 3).substr($number, -1);
        }

        return substr($number, 0, 4).str_repeat('*', max(2, $length - 6)).substr($number, -2);
    }
}
