<?php

namespace App\Models;

use App\Jobs\ProcessKnowledgeDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Ai\Stores;

class KnowledgeDocument extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_READY = 'ready';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'agent_id',
        'name',
        'file_path',
        'openai_file_id',
        'vector_store_document_id',
        'file_size',
        'mime_type',
        'status',
        'metadata',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'file_size' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (KnowledgeDocument $document) {
            ProcessKnowledgeDocument::dispatch($document);
        });

        static::deleting(function (KnowledgeDocument $document) {
            if ($document->vector_store_document_id && $document->agent?->vector_store_id) {
                try {
                    $store = Stores::get($document->agent->vector_store_id);
                    $store->remove($document->vector_store_document_id, deleteFile: true);
                }
                catch (\Throwable $e) {
                    Log::warning('Failed to remove document from vector store', [
                        'document_id' => $document->id,
                        'vector_store_document_id' => $document->vector_store_document_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if ($document->file_path) {
                Storage::delete($document->file_path);
            }
        });
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
