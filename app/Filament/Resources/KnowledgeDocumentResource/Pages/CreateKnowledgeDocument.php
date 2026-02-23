<?php

namespace App\Filament\Resources\KnowledgeDocumentResource\Pages;

use App\Filament\Resources\KnowledgeDocumentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CreateKnowledgeDocument extends CreateRecord
{
    protected static string $resource = KnowledgeDocumentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $filePath = $data['file_path'];
        $diskName = config('filesystems.default');
        $disk = Storage::disk();

        Log::info('CreateKnowledgeDocument uploading', [
            'file_path' => $filePath,
            'disk' => $diskName,
            'file_exists' => $disk->exists($filePath),
        ]);

        $data['name'] = basename($filePath);
        $data['file_size'] = $disk->size($filePath);
        $data['mime_type'] = $disk->mimeType($filePath) ?: 'application/octet-stream';
        $data['uploaded_by'] = auth()->id();
        $data['status'] = 'pending';

        return $data;
    }
}
