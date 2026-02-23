<?php

namespace App\Filament\Widgets;

use App\Models\Agent;
use App\Models\ConversationMessage;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class AgentPerformanceOverview extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected static bool $isDiscovered = true;

    public function table(Table $table): Table
    {
        $startDate = now()->subDays(7);
        $endDate = now();

        return $table
            ->query(
                Agent::query()
                    ->withSum(['metrics as total_tokens' => function (Builder $query) use ($startDate, $endDate) {
                        $query->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                    }], 'total_tokens')
                    ->withSum(['metrics as total_messages_sum' => function (Builder $query) use ($startDate, $endDate) {
                        $query->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                    }], 'total_messages')
                    ->withSum(['metrics as total_conversations_sum' => function (Builder $query) use ($startDate, $endDate) {
                        $query->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                    }], 'total_conversations')
                    ->withSum(['metrics as unique_users_sum' => function (Builder $query) use ($startDate, $endDate) {
                        $query->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                    }], 'unique_users')
                    ->active()
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Agent')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('model')
                    ->label('Model')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('total_messages_sum')
                    ->label('Messages (7d)')
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0))
                    ->sortable(),

                TextColumn::make('total_conversations_sum')
                    ->label('Conversations (7d)')
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0))
                    ->sortable(),

                TextColumn::make('total_tokens')
                    ->label('Tokens (7d)')
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0))
                    ->sortable(),

                TextColumn::make('unique_users_sum')
                    ->label('Unique Users (7d)')
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0))
                    ->sortable(),

                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Inactive')
                    ->sortable(),
            ])
            ->defaultSort('total_messages_sum', 'desc')
            ->paginated(false);
    }
}
