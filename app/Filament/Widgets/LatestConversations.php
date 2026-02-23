<?php

namespace App\Filament\Widgets;

use App\Models\Conversation;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestConversations extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Conversation::query()
                    ->with('user')
                    ->withCount('messages')
                    ->latest('updated_at')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Conversation')
                    ->limit(60)
                    ->searchable()
                    ->wrap(),

                TextColumn::make('user.email')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('messages_count')
                    ->label('Messages')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Started')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Last Activity')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc');
    }
}
