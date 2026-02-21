<?php

namespace App\Filament\Resources\ConversationResource\Pages;

use App\Filament\Resources\ConversationResource;
use App\Models\ConversationMessage;
use Filament\Resources\Pages\ViewRecord;

class ViewConversation extends ViewRecord
{
    protected static string $resource = ConversationResource::class;

    protected function getViewData(): array
    {
        $messages = ConversationMessage::where('conversation_id', $this->record->id)
            ->orderBy('created_at')
            ->get();

        return [
            'messages' => $messages,
        ];
    }
}
