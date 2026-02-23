<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConversationResource\Pages;
use App\Models\Conversation;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ConversationResource extends Resource
{
    protected static ?string $model = Conversation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Conversations';

    protected static string|\UnitEnum|null $navigationGroup = 'Management';

    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title')
                    ->label('Conversation Title')
                    ->columnSpanFull(),

                TextEntry::make('user.email')
                    ->label('User'),

                TextEntry::make('created_at')
                    ->label('Started')
                    ->dateTime('M d, Y H:i'),

                RepeatableEntry::make('messages')
                    ->label('Messages')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('role')
                            ->label('Role')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'user' => 'gray',
                                'assistant' => 'primary',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                        TextEntry::make('agent')
                            ->label('Agent')
                            ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-'),

                        TextEntry::make('created_at')
                            ->label('Time')
                            ->dateTime('H:i:s'),

                        TextEntry::make('content')
                            ->label('Message')
                            ->columnSpanFull()
                            ->markdown()
                            ->prose(),

                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(60)
                    ->wrap(),

                TextColumn::make('user.email')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('messages_count')
                    ->counts('messages')
                    ->label('Messages')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label('Started')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Last Activity')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('user_type')
                    ->options([
                        'guest' => 'Guest',
                        'admin' => 'Admin',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'guest') {
                            $query->whereHas('user', fn ($q) => $q->where('is_guest', true));
                        } elseif ($data['value'] === 'admin') {
                            $query->whereHas('user', fn ($q) => $q->where('is_guest', false));
                        }
                    }),
            ])
            ->recordUrl(fn ($record) => static::getUrl('view', ['record' => $record]));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConversations::route('/'),
            'view' => Pages\ViewConversation::route('/{record}'),
        ];
    }
}
