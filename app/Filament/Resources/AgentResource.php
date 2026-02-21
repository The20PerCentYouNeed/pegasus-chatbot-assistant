<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgentResource\Pages;
use App\Models\Agent;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AgentResource extends Resource
{
    protected static ?string $model = Agent::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationLabel = 'Agents';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255),

            TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            TextInput::make('agent_type')
                ->label('Agent Class')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255)
                ->disabled(fn (?Agent $record) => $record !== null),

            Textarea::make('system_prompt')
                ->required()
                ->rows(10)
                ->columnSpanFull(),

            Select::make('provider')
                ->options([
                    'openai' => 'OpenAI',
                    'anthropic' => 'Anthropic',
                ])
                ->default('openai')
                ->required(),

            TextInput::make('model')
                ->required()
                ->default('gpt-4o-mini'),

            TextInput::make('temperature')
                ->numeric()
                ->minValue(0)
                ->maxValue(1)
                ->step(0.1)
                ->default(0.7),

            TextInput::make('max_tokens')
                ->numeric()
                ->default(4096),

            TextInput::make('max_steps')
                ->numeric()
                ->default(10),

            TextInput::make('vector_store_id')
                ->label('OpenAI Vector Store ID')
                ->maxLength(255),

            TextInput::make('max_conversation_messages')
                ->numeric()
                ->default(50),

            Toggle::make('is_active')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('model'),
                TextColumn::make('provider'),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('knowledge_documents_count')
                    ->counts('knowledgeDocuments')
                    ->label('Docs'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgents::route('/'),
            'create' => Pages\CreateAgent::route('/create'),
            'edit' => Pages\EditAgent::route('/{record}/edit'),
        ];
    }
}
