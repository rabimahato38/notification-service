<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'whats_app_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_id',
        'service_name',
        'to',
        'template',
        'language',
        'parameters',
        'response',
        'status_code',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'parameters' => 'array',
            'response' => 'array',
            'status_code' => 'integer',
        ];
    }
}
