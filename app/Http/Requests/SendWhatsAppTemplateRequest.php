<?php

namespace App\Http\Requests;

use App\Models\Service;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SendWhatsAppTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_id' => ['required', 'string', 'max:255'],
            'to' => ['required', 'string'],
            'template' => ['required', 'string'],
            'language' => ['nullable', 'string'],
            'parameters' => ['nullable', 'array'],
            'parameters.*' => ['string'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'language' => $this->input('language') ?: 'en',
            'parameters' => $this->input('parameters', []),
        ]);
    }

    /**
     * Get the service ID.
     */
    public function getServiceId(): string
    {
        return (string) $this->validated('service_id');
    }

    /**
     * Get the service name resolved from database or input.
     */
    public function getServiceName(): string
    {
        $serviceId = $this->getServiceId();
        $service = $this->attributes->get('validated_service')
            ?? Service::where('service_id', $serviceId)->first();

        if ($service) {
            return $service->service_name;
        }

        return (string) ($this->input('service_name') ?? $serviceId);
    }

    /**
     * Get the normalized language code.
     */
    public function getLanguage(): string
    {
        return (string) ($this->validated('language') ?? 'en');
    }

    /**
     * Get the normalized parameters array.
     */
    public function getParameters(): array
    {
        return (array) ($this->validated('parameters') ?? []);
    }
}
