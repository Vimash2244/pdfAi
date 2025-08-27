<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdfParse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ai_model_id',
        'original_filename',
        'stored_filename',
        'file_size_bytes',
        'parse_result',
        'error_message',
        'status',
        'processed_at',
    ];

    protected $casts = [
        'parse_result' => 'array',
        'processed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function aiModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
