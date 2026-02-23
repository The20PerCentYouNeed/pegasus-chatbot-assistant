<?php

namespace App\Filament\Pages;

use App\Models\DashboardSetting;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.settings';

    protected static ?string $navigationLabel = 'Settings';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 6;

    protected static ?string $title = 'Dashboard Settings';

    protected static ?string $description = 'Configure your dashboard preferences';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'alert_token_budget_daily' => DashboardSetting::get('alert_token_budget_daily', 100000),
            'alert_token_budget_monthly' => DashboardSetting::get('alert_token_budget_monthly', 2000000),
            'alert_error_rate' => DashboardSetting::get('alert_error_rate', 5),
            'alert_max_messages_per_hour' => DashboardSetting::get('alert_max_messages_per_hour', 500),

            'enable_token_alerts' => DashboardSetting::get('enable_token_alerts', true),
            'enable_error_alerts' => DashboardSetting::get('enable_error_alerts', true),
            'enable_rate_alerts' => DashboardSetting::get('enable_rate_alerts', false),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Section::make('Alert Thresholds')
                        ->description('Configure when to trigger performance alerts')
                        ->icon('heroicon-o-bell-alert')
                        ->schema([
                            TextInput::make('alert_token_budget_daily')
                                ->label('Daily Token Budget')
                                ->numeric()
                                ->minValue(1000)
                                ->maxValue(10000000)
                                ->step(1000)
                                ->helperText('Alert when daily token usage exceeds this amount'),

                            TextInput::make('alert_token_budget_monthly')
                                ->label('Monthly Token Budget')
                                ->numeric()
                                ->minValue(10000)
                                ->maxValue(100000000)
                                ->step(10000)
                                ->helperText('Alert when monthly token usage exceeds this amount'),

                            TextInput::make('alert_error_rate')
                                ->label('Error Rate Alert (%)')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(20)
                                ->step(1)
                                ->suffix('%')
                                ->helperText('Alert when error rate exceeds this percentage'),

                            TextInput::make('alert_max_messages_per_hour')
                                ->label('Max Messages per Hour')
                                ->numeric()
                                ->minValue(10)
                                ->maxValue(10000)
                                ->step(10)
                                ->helperText('Alert when message rate exceeds this threshold'),
                        ])->columns(2),

                    Section::make('General Configuration')
                        ->description('Enable or disable alert categories')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            Toggle::make('enable_token_alerts')
                                ->label('Token Budget Alerts')
                                ->helperText('Notify when token usage approaches budget limits')
                                ->inline(false),

                            Toggle::make('enable_error_alerts')
                                ->label('Error Rate Alerts')
                                ->helperText('Notify when error rates exceed thresholds')
                                ->inline(false),

                            Toggle::make('enable_rate_alerts')
                                ->label('Message Rate Alerts')
                                ->helperText('Notify when message throughput is unusually high')
                                ->inline(false),
                        ])->columns(2),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Save changes')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            foreach ($data as $key => $value) {
                DashboardSetting::set($key, $value);
            }

            Notification::make()
                ->title('Settings saved successfully')
                ->success()
                ->send();
        } catch (Exception $e) {
            Notification::make()
                ->title('An error occurred')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
