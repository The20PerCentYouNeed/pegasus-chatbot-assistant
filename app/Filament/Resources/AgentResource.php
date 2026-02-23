<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgentResource\Pages;
use App\Models\Agent;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AgentResource extends Resource
{
    protected static ?string $model = Agent::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationLabel = 'Agents';

    protected static string|\UnitEnum|null $navigationGroup = 'Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Basic Information')
                    ->icon('heroicon-o-information-circle')
                    ->description('General agent details and identification')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Customer Support Bot'),

                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('agent_type')
                            ->label('Agent Class')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->disabled(fn (?Agent $record) => $record !== null)
                            ->helperText('Fully qualified class name of the agent'),

                        Toggle::make('is_active')
                            ->default(true)
                            ->inline(false),
                    ])->columns(2),

                Section::make('AI Configuration')
                    ->icon('heroicon-o-cpu-chip')
                    ->description('Configure the AI model and behavior parameters')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('provider')
                            ->options([
                                'openai' => 'OpenAI',
                                'anthropic' => 'Anthropic',
                            ])
                            ->default('openai')
                            ->required()
                            ->native(false),

                        TextInput::make('model')
                            ->required()
                            ->default('gpt-4o-mini')
                            ->helperText('Model identifier (e.g., gpt-4o-mini, claude-3-sonnet)'),

                        TextInput::make('temperature')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1)
                            ->step(0.1)
                            ->default(0.7)
                            ->helperText('0 = Focused & Deterministic | 1 = Creative & Random'),

                        TextInput::make('max_tokens')
                            ->numeric()
                            ->default(4096)
                            ->helperText('Maximum length of agent responses'),

                        TextInput::make('max_steps')
                            ->numeric()
                            ->default(10)
                            ->helperText('Maximum tool-use steps per turn'),

                        TextInput::make('max_conversation_messages')
                            ->numeric()
                            ->default(50)
                            ->helperText('Context window message limit'),
                    ])->columns(2),

                Section::make('System Instructions')
                    ->icon('heroicon-o-document-text')
                    ->description('Define how the agent should behave and respond')
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('system_prompt')
                            ->required()
                            ->rows(10)
                            ->columnSpanFull()
                            ->placeholder("You are a helpful assistant that...\n\nExample:\n- Be friendly and professional\n- Answer questions clearly"),
                    ]),

                Section::make('Advanced')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->description('Technical configuration and integrations')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('vector_store_id')
                            ->label('OpenAI Vector Store ID')
                            ->maxLength(255)
                            ->helperText('Connect a vector store for RAG capabilities'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->url(fn ($record) => static::getUrl('edit', ['record' => $record])),

                TextColumn::make('model')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('provider')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'openai' => 'success',
                        'anthropic' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->sortable(),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),

                TextColumn::make('knowledge_documents_count')
                    ->counts('knowledgeDocuments')
                    ->label('Docs')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('provider')
                    ->options([
                        'openai' => 'OpenAI',
                        'anthropic' => 'Anthropic',
                    ]),
            ])
            ->recordUrl(fn ($record) => static::getUrl('edit', ['record' => $record]))
            ->defaultSort('created_at', 'desc');
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
