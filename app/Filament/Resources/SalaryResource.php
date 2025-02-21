<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalaryResource\Pages;
use App\Filament\Resources\SalaryResource\RelationManagers;
use App\Models\Employee;
use App\Models\Salary;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

class SalaryResource extends Resource
{
    protected static ?string $model = Salary::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()
                ->schema([
                    Select::make('employee_id')
                    ->label('Employee')
                    ->options(fn () => Employee::all()->mapWithKeys(fn ($employee) => [
                        $employee->id => $employee->first_name . ' ' . $employee->last_name
                        ]))
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $employee = Employee::find($state);
                                $set('basic_salary', $employee?->basic_salary ?? 0);
                            }
                        }),


                    ])
                    ->columnSpanFull(),

            Section::make()
                ->schema([
                    TextInput::make('basic_salary')
                        ->numeric()
                        ->required()
                        ->disabled(fn ($context) => $context === 'edit') // Only disable in edit mode
                        ->dehydrated(), // Ensure it's included in the form submission

                    TextInput::make('house_allowance')
                        ->numeric()
                        ->default(0)
                        ->reactive(),

                    TextInput::make('transport_allowance')
                        ->numeric()
                        ->default(0)
                        ->reactive(),

                    Repeater::make('extra_earnings')
                        ->schema([
                            TextInput::make('name')->required(),
                            TextInput::make('amount')->numeric()->required(),
                        ])->columns(2)
                        ->label('Additional Earnings')
                        ->columnSpanFull()
                        ->reactive(),
                ])
                ->columns(2),

            Section::make()
                ->schema([
                    TextInput::make('paye_tax')
                        ->numeric()
                        ->required()
                        ->reactive(),

                    TextInput::make('sha_contribution')
                        ->numeric()
                        ->required()
                        ->reactive(),

                    TextInput::make('nssf_contribution')
                        ->numeric()
                        ->required()
                        ->reactive(),

                    Repeater::make('extra_deductions')
                        ->schema([
                            TextInput::make('name')->required(),
                            TextInput::make('amount')->numeric()->required(),
                        ])->columns(2)
                        ->label('Additional Deductions')
                        ->columnSpanFull()
                        ->reactive(),
                ])
                ->columns(2),

                Section::make()
                ->schema([
                    Placeholder::make('net_salary_placeholder')
                        ->label('Net Salary')
                        ->content(function (Get $get, Set $set) {
                            $total = (float) ($get('basic_salary') ?? 0) +
                                     (float) ($get('house_allowance') ?? 0) +
                                     (float) ($get('transport_allowance') ?? 0) +
                                     (float) collect($get('extra_earnings') ?? [])->sum(fn ($item) => (float) $item['amount']) -
                                     ((float) ($get('paye_tax') ?? 0) +
                                     (float) ($get('sha_contribution') ?? 0) +
                                     (float) ($get('nssf_contribution') ?? 0) +
                                     (float) collect($get('extra_deductions') ?? [])->sum(fn ($item) => (float) $item['amount']));

                            $set('net_salary', $total); // Store the value in the hidden input

                            return Number::currency($total, 'KES'); // Display formatted salary
                        }),

                    Hidden::make('net_salary')
                        ->default(0) // Default value to prevent null issues
                        ->dehydrated() // Ensure it's stored in the database
                        ->live(), // Ensure real-time updates
                    ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.first_name')
    ->label('First Name')
    ->sortable()
    ->searchable(),

TextColumn::make('employee.last_name')
    ->label('Last Name')
    ->sortable()
    ->searchable(),

                TextColumn::make('basic_salary')->money('KES'),
                TextColumn::make('paye_tax')->money('KES'),
                TextColumn::make('sha_contribution')->money('KES'),
                TextColumn::make('nssf_contribution')->money('KES'),
                TextColumn::make('net_salary')->money('KES'),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                    Action::make('Generate Payslip')
                        ->icon('heroicon-o-folder-arrow-down')
                        ->url(fn ($record) => route('payroll.pdf', $record->id))
                        ->openUrlInNewTab()
                        ->color('primary'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalaries::route('/'),
            'create' => Pages\CreateSalary::route('/create'),
            'edit' => Pages\EditSalary::route('/{record}/edit'),
        ];
    }
}
