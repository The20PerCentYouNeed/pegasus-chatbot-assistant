<?php

namespace App\Filament\Resources\KnowledgeDocumentResource\Pages;

use App\Filament\Resources\KnowledgeDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKnowledgeDocuments extends ListRecords
{
    protected static string $resource = KnowledgeDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
