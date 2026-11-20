<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_id',
        'service_name',
        'api_key',
        'is_active',
        'description',
        'allowed_templates',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'allowed_templates' => 'array',
    ];

    /**
     * Messages sent under this service (matched on the public service_id).
     */
    public function logs(): HasMany
    {
        return $this->hasMany(WhatsAppLog::class, 'service_id', 'service_id');
    }

    /**
     * Turn "a, b ,c" into ['a', 'b', 'c']; empty input means "all templates" (null).
     *
     * @return array<int, string>|null
     */
    public static function parseTemplateList(?string $input): ?array
    {
        $templates = array_values(array_unique(array_filter(array_map('trim', explode(',', (string) $input)))));

        return $templates === [] ? null : $templates;
    }

    /**
     * API key with the middle hidden, for display.
     */
    public function maskedApiKey(): string
    {
        return substr($this->api_key, 0, 13).str_repeat('•', 8).substr($this->api_key, -4);
    }

    /**
     * Check if a template is allowed/assigned under this service.
     */
    public function isTemplateAllowed(string $templateName): bool
    {
        if (empty($this->allowed_templates)) {
            return true;
        }

        if (in_array('*', $this->allowed_templates, true)) {
            return true;
        }

        return in_array($templateName, $this->allowed_templates, true);
    }

    /**
     * Boot model events.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Service $service): void {
            if (empty($service->service_id)) {
                $service->service_id = static::generateUniqueServiceId();
            }

            if (empty($service->api_key)) {
                $service->api_key = static::generateUniqueApiKey();
            }
        });
    }

    /**
     * Generate a unique service ID.
     */
    public static function generateUniqueServiceId(): string
    {
        do {
            $serviceId = 'SRV-'.strtoupper(Str::random(8));
        } while (static::where('service_id', $serviceId)->exists());

        return $serviceId;
    }

    /**
     * Generate a unique gateway API key for WhatsApp gateway.
     */
    public static function generateUniqueApiKey(): string
    {
        do {
            $apiKey = 'wsg_live_'.Str::random(32);
        } while (static::where('api_key', $apiKey)->exists());

        return $apiKey;
    }
}
