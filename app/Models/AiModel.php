<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'model_identifier',
        'description',
        'is_active',
        'config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getApiKey(): ?string
    {
        return $this->config['api_key'] ?? null;
    }

    public function getEndpoint(): ?string
    {
        return $this->config['endpoint'] ?? null;
    }
}
