<?php

namespace App\Filament\Widgets;

use App\Models\Conversation;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class PopularTopicsWidget extends TableWidget
{
    protected static ?string $heading = 'Popular Topics';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Conversation::query()
                    ->selectRaw('title, COUNT(*) as occurrences')
                    ->groupBy('title')
                    ->orderByDesc('occurrences')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Topic'),
                TextColumn::make('occurrences')
                    ->label('Count')
                    ->sortable(),
            ]);
    }
}
