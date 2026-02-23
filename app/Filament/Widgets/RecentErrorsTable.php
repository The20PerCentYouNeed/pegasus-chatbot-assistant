<?php

namespace App\Filament\Widgets;

use App\Models\ConversationMessage;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentErrorsTable extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static bool $isDiscovered = false;

    protected static ?string $heading = 'Recent Errors';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ConversationMessage::query()
                    ->where('role', 'assistant')
                    ->where(function ($query) {
                        $query->whereNull('content')
                            ->orWhere('content', '');
                    })
                    ->with('conversation', 'conversation.user')
            )
            ->paginated([10, 20, 50])
            ->defaultPaginationPageOption(20)
            ->columns([
                TextColumn::make('agent')
                    ->label('Agent')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('conversation.title')
                    ->label('Conversation')
                    ->limit(50)
                    ->searchable()
                    ->wrap(),

                TextColumn::make('conversation.user.email')
                    ->label('User')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('content')
                    ->label('Response')
                    ->default('(empty)')
                    ->limit(40)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Occurred At')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No issues detected')
            ->emptyStateDescription('Everything is running smoothly! No empty responses have been detected.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
