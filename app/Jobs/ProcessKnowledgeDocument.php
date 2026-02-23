<?php

namespace App\Jobs;

use App\Models\KnowledgeDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Files\Document;
use Laravel\Ai\Stores;

class ProcessKnowledgeDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        public KnowledgeDocument $knowledgeDocument,
    ) {
    }

    public function handle(): void
    {
        $document = $this->knowledgeDocument;
        $agent = $document->agent;

        Log::info('ProcessKnowledgeDocument starting', [
            'document_id' => $document->id,
            'file_path' => $document->file_path,
            'default_disk' => config('filesystems.default'),
            'file_exists' => \Illuminate\Support\Facades\Storage::exists($document->file_path),
        ]);

        $document->update(['status' => KnowledgeDocument::STATUS_PROCESSING]);

        if (!$agent->vector_store_id) {
            $store = Stores::create($agent->name . ' Knowledge Base');
            $agent->update(['vector_store_id' => $store->id]);
        }

        $store = Stores::get($agent->vector_store_id);

        $response = $store->add(Document::fromStorage($document->file_path));

        $document->update([
            'openai_file_id' => $response->fileId(),
            'vector_store_document_id' => $response->id(),
            'status' => KnowledgeDocument::STATUS_READY,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        $this->knowledgeDocument->update([
            'status' => KnowledgeDocument::STATUS_FAILED,
        ]);

        Log::error('ProcessKnowledgeDocument failed', [
            'document_id' => $this->knowledgeDocument->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
